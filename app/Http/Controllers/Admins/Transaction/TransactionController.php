<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins\Transaction;

use App\DTO\CallbackWithdrawFiatDTO;
use App\DTO\CryptoWithdrawDto;
use App\DTO\FiatWithdrawDto;
use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Transaction\DepositStore;
use App\Http\Requests\Admins\Transaction\Query;
use App\Http\Requests\Admins\Transaction\WithdtrawStore;
use App\Http\Requests\Admins\Transaction\WithdtrawUpdate;
use App\Models\Blockchain;
use App\Models\Deposit;
use App\Models\ManualDeposit;
use App\Models\Order;
use App\Models\Symbol;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\interfaces\ICallbackWithdrawFiatService;
use App\Transformers\CallbackWithdrawFiatTransformer;
use App\Transformers\WithdrawFiatTransformer;
use App\Webservices\AtipayWithdraw\interfaces\IAtipayWithdrawService;
use App\Webservices\CryptoWithdraw\interfaces\ICryptoWithdrawService;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class TransactionController extends Controller
{
    const BROADCAST = 'pushed';
    private ICryptoWithdrawService $cryptoWithdrawService;
    private IAtipayWithdrawService $atipayWithdrawService;
    private ICallbackWithdrawFiatService $callbackWithdrawFiatService;

    /**
     * TransactionController constructor.
     */

    public function __construct(
        ICryptoWithdrawService $cryptoWithdrawService,
        IAtipayWithdrawService $atipayWithdrawService,
        ICallbackWithdrawFiatService $callbackWithdrawFiatService
    ) {
        $this->middleware(['auth:admin']);

        $this->middleware('permission:deposit')->only(
            ['deposits', 'depositStore', 'manualDeposits', 'manualDepositUpdate']
        );

        $this->middleware('permission:withdraw')->only(['withdraws', 'withdrawUpdate']);

        $this->cryptoWithdrawService = $cryptoWithdrawService;

        $this->atipayWithdrawService = $atipayWithdrawService;

        $this->callbackWithdrawFiatService = $callbackWithdrawFiatService;
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function requireAttentionCount(): JsonResponse
    {
        try {
            return response()->json([
                'require-attention-count' => Withdraw::whereStatus(1)->count()
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

    public function getDeposits(): JsonResponse
    {
        try {
            return response()->json([
                'deposits' => Deposit::isVerified()->with(['user', 'admin', 'symbol'])->latest()->get()
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

    public function manualDeposits(): JsonResponse
    {
        try {
            return response()->json([
                'manual-deposits' => ManualDeposit::query()->with(['admin', 'user'])->latest()->get(),
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws \JsonException
     */
    public function manualDepositUpdate(Request $request, ManualDeposit $manualDeposit): JsonResponse
    {
        /**
         * Check if manual deposit is able to edit or not.
         */

        if ($manualDeposit->status != 1) {
            return response()->json([
                'error' => __('messages.wallets.deposit.update.failed'),
            ], 404);
        }

        /**
         * Validate request.
         */

        $this->validate($request, [
            'status' => 'required|boolean'
        ]);
        DB::beginTransaction();
        try {
            /**
             * Update the row.
             */

            $manualDeposit->update([
                'status' => $request->status ? 2 : 0
            ]);

            /**
             * Associate the admin.
             */

            $manualDeposit->admin()->associate($this->admin())->save();

            if ($request->status) {
                /**
                 * Submit and charge the wallet if manual deposit had been approved.
                 */

                $deposit = $manualDeposit->user()->first()->deposits()->create([
                    'quantity' => $manualDeposit->quantity,
                    'ref' => $manualDeposit->ref,
                    'currency' => $manualDeposit->currency,
                    'status' => true,
                ]);

                $deposit->admin()->associate($this->admin())->save();

                $userAvailableWallet = $manualDeposit->user()->first()->wallets()->firstOrCreate([
                    'currency' => $manualDeposit->currency,
                    'type' => 1,
                    'is_virtual' => false
                ]);

                $userAvailableWallet->quantity += $manualDeposit->quantity;

                $userAvailableWallet->save();
            }

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.wallets.deposit.update.successful'),
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @return Authenticatable
     */

    private function admin(): Authenticatable
    {
        return auth('admin')->user();
    }

    /**
     * @param DepositStore $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function depositStore(DepositStore $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            /**
             * Fetch user.
             */

            $user = User::findOrFail($request->user_id);

            /**
             * Fetch symbol.
             */

            $symbol = Symbol::isDepositable()->findOrFail($request->symbol_id);

            /**
             * Submit deposit.
             */

            $deposit = $user->deposits()->create([
                'quantity' => $request->quantity,
                'symbol_id' => $symbol->id,
                'ref' => $request->ref,
                'status' => true
            ]);

            /**
             * Submit the admin that submitted the deposit.
             */

            $deposit->admin()->associate($this->admin())->save();

            /**
             * Add to available wallet.
             */

            $user->setWallet($symbol->id, 1, false)->chargeWallet($request->quantity);

            /**
             * Return response.
             */
            DB::commit();
            return response()->json([
                'message' => __('messages.wallets.deposit.successful'),
                'deposit' => $deposit
            ], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'errorr' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function getWithdraws(): JsonResponse
    {
        try {
            return response()->json([
                'withdraws' => Withdraw::with(['user', 'admin', 'symbol'])->latest()->get(),
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
             * Let's filter query type.
             */

            if ($request->query_type === 'deposit') {
                $query = Deposit::with(['user', 'symbol'])->isVerified();
            } elseif ($request->query_type === 'withdraw') {
                $query = Withdraw::with(['user', 'symbol']);
            } elseif ($request->query_type === 'order') {
                $query = Order::with(['user', 'trades']);
            }

            /**
             * Affect times.
             */

            if ($request->start_date) {
                $query = $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query = $query->whereDate('created_at', '<=', $request->end_date);
            }


            /**
             * Affect user filter.
             */

            if ($request->users) {
                $query = $query->whereIn('user_id', $request->users);
            }

            /**
             * Return response.
             */

            return response()->json([
                'report' => $query->latest()->get()
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Order $order
     * @return JsonResponse
     * @throws \JsonException
     */

    public function showOrder(Order $order): JsonResponse
    {
        try {
            return response()->json([
                'order' => $order->with(['trades', 'user'])->findOrFail($order->id)
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param WithdtrawUpdate $request
     * @param Withdraw $withdraw
     * @return JsonResponse
     * @throws \JsonException
     */

    public function withdrawUpdate(WithdtrawUpdate $request, Withdraw $withdraw): JsonResponse
    {
        DB::beginTransaction();
        try {
            /**
             * Check if new status of withdraw is same as current status of withdraw or what.
             */

            if ($withdraw->status != 1) {
                throw new Exception(__('messages.wallets.withdraw.update.failed'));
            }

            /**
             * Define user.
             */

            $user = $withdraw->user()->first();
            if(!$user instanceof User){
                throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
            }

            /**
             * Define symbol.
             */

            $symbol = $withdraw->symbol()->first();

            $data = [];
            $cryptoWithdrawResult = [];

            if ($request->is_approved) {
                if ($request->type == 'fiat') {
                    $result = $this->fiatWithdraw($withdraw);
                    if ($result['success'] == 'true') {
                        $data = $result['data'];
                        $data['withdraw_id'] = $withdraw->id;
                        $data['status'] = 2;
                        $data['ref'] = $result['data']['referenceId'];
                        $callbackWithdrawFiatDTO = new CallbackWithdrawFiatDTO($data);
                        $this->callbackWithdrawFiatService->create($callbackWithdrawFiatDTO);

                    } else {
                        if ($result['enErrorMessage'] == 'Invalid destination iban number/ach-transfer') {
                            $data['error_withdraw_transaction'] = Lang::get(
                                'messages.atipay.withdraw-fiat.' . $result['enErrorMessage']
                            );
                            $data['error_withdraw'] = $data['error_withdraw_transaction'];
                        } else {
                            $data['error_withdraw_transaction'] = $result['enErrorMessage'];
                            $data['error_withdraw'] = Lang::get('messages.failed');
                        }
                    }

                } elseif($request->type == 'crypto') {
                    $blockchain_title = Blockchain::where(
                        'title',
                        trim(str_before($withdraw->destination, ':'))
                    )->first()->name_en;

                    $inputs = [
                        'public_key' => $request['public_key'],
                        'private_key' => $request['private_key'],
                        'destination' => $withdraw->destination,
                        'quantity' => $withdraw->quantity,
                        'wage_quantity' => $withdraw->wage_quantity,
                        'symbol' => $withdraw->symbol->title,
                        'blockchain' => $blockchain_title,
//                        'fee'   => $this->calculateFee($blockchain_title, $withdraw->symbol->title),
                        'fee' => $request['fee'],
                        'node' => $request['provider'],
                    ];

                    $withdrawData = CryptoWithdrawDto::toCryptoWithdrawDto($inputs);
                    // Crypto withdraw transaction from withdraw service with rabbitmq
//                    $result = event(CryptoWithdrawJob::dispatch($withdrawData));

                    //Crypto withdraw transaction from withdraw service with Api

                    $cryptoWithdrawResult = $this->cryptoWithdrawService->withdraw($withdrawData);

                    if (empty($cryptoWithdrawResult)) {
                        Log::channel('stderr')->error('cryptoWithdrawResult must be of type array, null given');
                        throw new NotFoundHttpException('cryptoWithdrawResult must be of type array, null given');
                    } elseif (isset($cryptoWithdrawResult['data']['status']) && ($cryptoWithdrawResult['data']['status'] ?? null) != self::BROADCAST) {
                        Log::channel('stderr')->error(
                            'cryptoWithdrawResult is not in ' . self::BROADCAST . ' status. Current status: ' . $cryptoWithdrawResult['data']['status']
                        );
                        throw new NotFoundHttpException(
                            'cryptoWithdrawResult is not in ' . self::BROADCAST . ' status. Current status: ' . $cryptoWithdrawResult['data']['status']
                        );
                    }

                    if (!empty($cryptoWithdrawResult['errors'])) {
                        $data['error_withdraw_transaction'] = json_encode($cryptoWithdrawResult['errors']);
                    } else {
                        $data['status'] = 2;
                        $data['ref'] = $cryptoWithdrawResult['data']['hash'];
                        $data['error_withdraw_transaction'] = null;
                    }
                }

            } else {
                $data['status'] = 0;

                $data['reject_reason'] = $request->reject_reason;

                /**
                 * Return subtracted quantity to available wallet.
                 */

                $user->setWallet($symbol->id, 1, false)->chargeWallet($withdraw->quantity + $withdraw->wage_quantity);

            }

            if (empty($cryptoWithdrawResult['errors']) && empty($data['error_withdraw_transaction'])) {
                /**
                 * Subtract from frozen wallet of user.
                 */

                $user->setWallet($symbol->id, 2, false)->chargeWallet(
                    -($withdraw->quantity + $withdraw->wage_quantity)
                );

                /**
                 * Update withdraw.
                 */

                $withdraw->update($data);

                /**
                 * Associate the admin.
                 */
            }

            $withdraw->admin()->associate($this->admin())->save();

            /**
             * Return response.
             */
            if (!empty($cryptoWithdrawResult['errors'])) {
                $withdraw->update(['error_withdraw_transaction' => $cryptoWithdrawResult['errors']]);
                DB::commit();
                Logger::error('Error happened!', $cryptoWithdrawResult['errors']);
                return response()->json([
                    'error' => $cryptoWithdrawResult['errors'],
                ], 400);

            } elseif (!empty($data['error_withdraw_transaction'])) {
                $withdraw->update(['error_withdraw_transaction' => $data['error_withdraw_transaction']]);
                DB::commit();
                Logger::error('Error happened!', $data['error_withdraw_transaction']);
                return response()->json([
                    'error' => $data['error_withdraw'],
                ], 400);

            } else {
                DB::commit();
                return response()->json([
                    'message' => Lang::get('messages.wallets.withdraw.successful'),
                ]);
            }
        } catch (Throwable $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));

            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param WithdtrawStore $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function withdrawStore(WithdtrawStore $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            /**
             * Define user.
             */

            $user = User::findOrFail($request->user_id);

            /**
             * Define symbol.
             */

            $symbol = Symbol::isWithdrawable()->findOrFail($request->symbol_id);

            /**
             * Seek for user's available real wallet.
             */

            $userWallet = $user->setWallet($symbol->id, 1, false);

            if ($request->quantity > $userWallet->getWallet()->quantity) {
                throw new Exception(__('messages.wallets.withdraw.insufficientBalance'));
            }

            /**
             * Store new withdraw row.
             */

            $withdraw = $user->withdraws()->create(
                collect($request->only(['quantity', 'symbol_id', 'ref', 'destination']))->put('status', 2)->toArray()
            );

            /**
             * Associate admin.
             */

            $withdraw->admin()->associate($this->admin())->save();

            /**
             * Subtract quantity.
             */

            $userWallet->chargeWallet(-$request->quantity);
            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'withdraw' => $withdraw,
                'message' => __('messages.wallets.withdraw.successful')
            ], 201);
        } catch (Exception $exception) {
            DB::rollback();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param $withdraw
     * @return Collection
     */
    private function fiatWithdraw($withdraw): Collection
    {
        $inputs = WithdrawFiatTransformer::toWithdrawFiatDTO($withdraw);
        return $this->atipayWithdrawService->payaTransfer($inputs->toArray());
    }

    /**
     * @param $blockchain_title
     * @param $symbol
     * @return float|int
     */
    public function calculateFee($blockchain_title, $symbol): float|int
    {
        $blockchain_symbol = strtolower($blockchain_title . '-' . $symbol);

        return match ($blockchain_symbol) {
            "bitcoin-btc" => 0,
            "ethereum-usdt" => 100000,
            "tron-usdt" => 8000000,
            "tron-trx" => 0,
            "litecoin-ltc" => 0,
            default => 0,
        };
    }

}
