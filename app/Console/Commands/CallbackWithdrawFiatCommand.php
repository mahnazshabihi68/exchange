<?php

namespace App\Console\Commands;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Models\CallbackWithdrawFiat;
use App\Webservices\AtipayWithdraw\impls\AtipayWithdrawService;
use Illuminate\Console\Command;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Exception;

class CallbackWithdrawFiatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callback:withdraw-fiat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Callback withdraw fiat';

    private CallbackWithdrawFiat $callbackWithdrawFiat;
    private AtipayWithdrawService $atipayWithdrawService;

    public function __construct(
        CallbackWithdrawFiat $callbackWithdrawFiat,
        AtipayWithdrawService $atipayWithdrawService
    ) {
        parent::__construct();
        $this->callbackWithdrawFiat = $callbackWithdrawFiat;
        $this->atipayWithdrawService = $atipayWithdrawService;
    }

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle()
    {
        $callbackWithdrawFiats = $this->callbackWithdrawFiat::where('transfer_status', '!=', 'TRANSFERRED')->get();

        if(count($callbackWithdrawFiats) <= 0)
        {
            throw new NotFoundHttpException('Withdraw Not Found.');
        }

        foreach ($callbackWithdrawFiats as $callbackWithdrawFiat) {
            try {

                    $result = $this->atipayWithdrawService->payaTransferReport($callbackWithdrawFiat);
                    if($result['success'] != 'true' || $result['data']->totalRecord < 1 || count($result['data']->transactions) < 1) {
                        throw new NotFoundHttpException('Transaction with referenceId: ' . $callbackWithdrawFiat->reference_id . ' Not Found');
                    }
                    $transaction = $result['data']->transactions[0];
                    $this->callbackWithdrawFiat->where('id', $callbackWithdrawFiat->id)->update(
                        [
                            'transaction_id' => $transaction->id,
                            'resumeable' => $transaction->resumeable,
                            'transfer_status' => $transaction->status,
                            'cancelable' => $transaction->cancelable,
                            'changeable' => $transaction->changeable,
                            'suspendable' => $transaction->suspendable
                        ]
                    );

            } catch (Throwable $exception) {
                Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
                throw new NotFoundHttpException($exception->getMessage());
            }
        }

        $this->info($this->signature . ' has been lunched successfully at ' . now());
    }
}
