<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Wallet;

use App\Classes\AtiPay;
use App\Classes\PayPing;
use App\Classes\ZarinPal;
use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Wallet\DepositRequest;
use App\Http\Requests\Users\Wallet\Query;
use App\Http\Requests\Users\Wallet\WithdrawRequest;
use App\Models\Blockchain;
use App\Models\Deposit;
use App\Models\Symbol;
use App\Models\Withdraw;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    /**
     * WalletController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:user'])->except('depositCallback');

        $this->middleware('permission:deposit')->only(['depositRequest', 'manualDeposits', 'manualDepositStore']);

        $this->middleware('permission:withdraw')->only(['withdrawRequest', 'withdrawCancel']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {

            return response()->json([
                'wallets' => $this->user()->wallets()->latest()->get()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @return Authenticatable
     */

    private function user(): Authenticatable
    {
        return auth('user')->user();
    }

    /**
     * @param string $wallet
     * @return JsonResponse
     * @throws \JsonException
     */

    public function show(string $wallet): JsonResponse
    {
        try {

            return response()->json([
                'wallet' => $this->user()->wallets()->symbol($wallet)->firstOrFail()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Query $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function query(Query $request): JsonResponse
    {
        try {

            /**
             * Define wallets instance.
             */

            $wallets = $this->user()->wallets()->with(['symbol', 'symbol.blockchains']);

            /**
             * Apply filters.
             */

            if ($request->has('symbols')) {

                $wallets = $wallets->symbols($request->symbols);

            }

            if ($request->has('is_available')) {

                $wallets = match ((bool)$request->is_available) {
                    true => $wallets->isAvailable(),
                    false => $wallets->isFrozen()
                };

            }

            if ($request->has('is_virtual')) {

                $wallets = match ((bool)$request->is_virtual) {
                    true => $wallets->isVirtual(),
                    false => $wallets->isReal()
                };

            }

            /**
             * Return response.
             */

            return response()->json([
                'wallets' => $wallets->latest()->get()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function transactions(): JsonResponse
    {
        try {

            return response()->json([
                'deposits' => $this->user()->deposits()->isVerified()->latest()->get(),
                'manual-deposits' => $this->user()->manualDeposits()->latest()->get(),
                'withdraws' => $this->user()->withdraws()->latest()->get()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param DepositRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     * @throws \JsonException
     */

    public function depositRequest(DepositRequest $request): JsonResponse
    {
        try {

            /**
             * Symbol.
             */

            $symbol = Symbol::isDepositable()->findOrFail($request->symbol_id);

            /**
             * Handle IRT Online Payment.
             */

            if ($request->type === 'fiat') {
                DB::beginTransaction();
                try {

                    /**
                     * Check whether IRT deposit is enabled or not.
                     */

                    if (!config('settings.irt_deposit_gateway_is_enabled')) {

                        throw new Exception(__('messages.wallets.deposit.notAvailable'), 400);

                    }

                    /**
                     * Determine the gateway.
                     */

                    $paymentGateway = match (config('settings.irt_deposit_gateway')) {
                        'atipay'    => new AtiPay(),
                        'payping'   => new PayPing(),
                        'zarinpal'  => new ZarinPal(),
                    };

                    /**
                     * Create a new deposit row in database.
                     */

                    $IRTOnlineDeposit = $this->user()->deposits()->create([
                        'quantity' => $request->quantity,
                        'symbol_id' => $symbol->id,
                        'gateway' => get_class($paymentGateway)
                    ]);

                    /**
                     * Try to fetch payment url.
                     */

                    $paymentRequest = $paymentGateway->paymentRequest(
                        $request->quantity,
                        route('user.wallets.deposit-callback', ['depositId' => $IRTOnlineDeposit->internal_deposit_id]),
                        $IRTOnlineDeposit->internal_deposit_id
                    );

                    /**
                     * Return response.
                     */

                    if ($paymentGateway instanceof AtiPay) {

                        $output = [
                            'payment-token' => $paymentRequest['token'],
                            'should-post-to' => $paymentRequest['url']
                        ];

                    } elseif ($paymentGateway instanceof PayPing) {

                        $output = [
                            'payment-url' => $paymentRequest['payment-url']
                        ];

                    } elseif ($paymentGateway instanceof ZarinPal) {
                        $output = [
                            'authority' => $paymentRequest['authority'],
                            'payment-url' => $paymentRequest['url']
                        ];
                    }
                    DB::commit();
                    return response()->json($output);

                } catch (Exception $exception) {
                    DB::rollBack();
                    Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
                    return response()->json([
                        'error' => $exception->getMessage()
                    ], 400);

                }

            }

            /**
             * Handle blockchain deposit.
             */


            elseif ($request->type === 'crypto') {
                $blockchain = Blockchain::find($request->blockchain_id);
                if(!$blockchain instanceof Blockchain){
                    throw new NotFoundException(NotFoundException::BLOCKCHAIN_NOT_FOUND);
                }
                $walletAddress = $this->user()->getWalletAddress($blockchain);

            }

            /**
             * Return response.
             */

            return response()->json([
                'wallet-address' => $walletAddress
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws \JsonException
     */

    public function depositCallback(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        try {

            /**
             * We have to verify payment within PSP + Set deposit as verified + increase quantity of IRT Available Wallet of user.
             */

            /**
             * Let's begin with querying deposit row.
             */

            $deposit = Deposit::query()->where('internal_deposit_id', $request->depositId)->isNotVerified();

            if (!$deposit->exists()) {

              throw new NotFoundException(NotFoundException::DEPOSIT_NOT_FOUND);

            }

            $deposit = $deposit->first();

            /**
             * Define ref.
             */

            if($request->refid) $ref = $request->refid;
            elseif($request->referenceNumber) $ref = $request->referenceNumber;
            elseif($request->Authority) $ref = $request->Authority;

            if (!$ref) {

                throw new Exception();

            }

            /**
             * Verify deposit in PSP.
             */

            (new $deposit->gateway())->paymentVerify($ref, $deposit->quantity);

            /**
             * Increase quantity of wallet.
             */

            $deposit->user()->first()->setWallet($deposit->symbol()->first()->id, 1, false)->chargeWallet($deposit->quantity);

            /**
             * Set deposit as verified + update ref.
             */

            $deposit->update([
                'status' => true,
                'ref' => $ref
            ]);

            DB::commit();
            /**
             * Return to app.
             */

            return redirect()->to(config('app.frontend_url') . '/user/wallet/gateway-callback/' . $deposit->internal_deposit_id);

        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return redirect()->to(config('app.frontend_url') . '/user/wallet/gateway-callback');

        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function depositCallbackValidator(Request $request): JsonResponse
    {
        try {

            $this->validate($request, [
                'internal_deposit_id' => 'required|string'
            ]);

            $deposit = $this->user()->deposits()->isVerified()->where('internal_deposit_id', $request->internal_deposit_id);

            if (!$deposit->exists()) {

                throw new Exception(__('messages.wallets.deposit.online-payment.notFound'));

            }

            return response()->json([
                'deposit' => $deposit->select(['internal_deposit_id', 'quantity', 'ref', 'updated_at'])->first(),
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }


    /**
     * @param WithdrawRequest $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function withdrawRequest(WithdrawRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Find symbol.
             */

            $symbol = Symbol::isWithdrawable()->findOrFail($request->symbol_id);

            /**
             * Define withdraw Quantity.
             */

            $withdrawOriginalQty = $request->quantity;

            $withdrawWageQty = 0;

            /**
             * Fetch available Wallet of user.
             */

            $availableWallet = $this->user()->setWallet($symbol->id, 1, false);

            if ($request->type === 'fiat'){

                /**
                 * Fetch bank account.
                 */

                $bankAccount = $this->user()->bankAccounts()->findOrFail($request->bankAccount_id);

                $destination = $bankAccount->bank . ' : ' . $bankAccount->sheba . ' : ' . $bankAccount->card;
            }

            elseif ($request->type === 'crypto'){

                /**
                 * Find blockchain.
                 */

                $blockchain = $symbol->blockchains()->findOrFail($request->blockchain_id);

                /**
                 * Add blockchain fee.
                 */

                $withdrawWageQty = $blockchain->pivot->transfer_fee;

                $destination = $blockchain->title . ' : ' .$request->destination_wallet_address;

            }

            /**
             * Check sufficiency.
             */

            if ($availableWallet->getWallet()->quantity < ($withdrawOriginalQty + $withdrawWageQty)){

                throw new Exception(__('messages.wallets.withdraw.insufficientBalance'));

            }

            /**
             * Subtract quantity from available wallet.
             */

            $availableWallet->chargeWallet(-($withdrawOriginalQty + $withdrawWageQty));

            $this->user()->setWallet($symbol->id, 2, false)->chargeWallet($withdrawOriginalQty + $withdrawWageQty);

            /**
             * Store withdraw request.
             */

            $withdrawData = [
                'quantity'  =>  $withdrawOriginalQty,
                'wage_quantity' =>  $withdrawWageQty,
                'symbol_id' =>  $symbol->id,
                'destination'   =>  $destination,
            ];

            $this->user()->withdraws()->create($withdrawData);

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.wallets.withdraw.successful')
            ]);

        } catch (Exception $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }

    }

    /**
     * @param Withdraw $withdraw
     * @return JsonResponse
     * @throws \JsonException
     */

    public function withdrawCancel(Withdraw $withdraw): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Fetch withdraw
             */

            $withdraw = $this->user()->withdraws()->whereStatus(1)->where('id',$withdraw->id);

            if (!$withdraw->exists()) {

                throw new Exception(__('messages.wallets.withdraw.notFound'));

            }

            /**
             * Lets keep going by refund subtracted quantity and update withdraw request.
             */

            $withdraw = $withdraw->first();

            $withdraw->update([
                'status' => 3
            ]);

            /**
             * Fetch available wallet of user and return the quantity.
             */

            $symbol = $withdraw->symbol()->first();

            $this->user()->setWallet($symbol->id, 1, false)->chargeWallet($withdraw->quantity + $withdraw->wage_quantity);

            $this->user()->setWallet($symbol->id, 2, false)->chargeWallet(-($withdraw->quantity + $withdraw->wage_quantity));

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.wallets.withdraw.cancelWithdraw.successful')
            ]);

        } catch (Exception $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @return JsonResponse
     */

    public function manualDeposits(): JsonResponse
    {
        return response()->json([
            'manual-deposits' => $this->user()->manualDeposits()->latest()->get()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function manualDepositStore(Request $request): JsonResponse
    {
        /**
         * Let's begin with validation.
         */

        $this->validate($request, [
            'quantity' => 'required|numeric|gt:0',
            'symbol_id' => 'required|integer|exists:symbols,id',
            'ref' => 'required_without:picture|string|unique:deposits,ref|unique:manual_deposits,ref',
            'picture' => 'required_without:ref|image|max:1024',
        ]);

        $data = $request->except('picture');

        /**
         * Store the picture.
         */

        if ($request->hasFile('picture')) {

            $data['picture'] = $request->file('picture')->store('manualDeposits');

        }

        if (!$request->ref) {

            $data['ref'] = mt_rand(1000000000, 9999999999);

        }

        /**
         * Store manual deposit.
         */

        $this->user()->manualDeposits()->create($data);

        /**
         * Return response.
         */

        return response()->json([
            'message' => __('messages.wallets.deposit.successful'),
        ]);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function resetVirtualAssets(): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * The strategy is to remove all virtual wallets and create USDT Virtual wallet with the balance that admin had been set.
             */

            /**
             * Fetch and remove all virtual wallets.
             */

            $this->user()->wallets()->isVirtual()->delete();

            /**
             * Create new USDT Virtual wallet.
             */

            $this->user()->setWallet('USDT', 1, 1)->chargeWallet(config('settings.virtual_USDT_wallet_default_amount') ?? 0);

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.wallets.reset-virtual-assets.successful'),
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }

    }
}
