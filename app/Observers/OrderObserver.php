<?php

namespace App\Observers;

use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderObserver
{

    public function creating(Order $order)
    {
        $order->internal_order_id = Str::orderedUuid()->toString();
    }

    /**
     * @throws NotFoundException
     * @throws \JsonException
     * @throws \Throwable
     */
    public function updated(Order $order)
    {
        DB::beginTransaction();
        try{
            if ($order->side === 'BUY') {

                if ($order->fill_percentage == 100) {

                    $diff = match ($order->type) {
                        'MARKET' => ($order->original_quantity * $order->original_market_price) - ($order->executed_quantity * $order->executed_price),
                        default => ($order->original_quantity * $order->original_price) - ($order->executed_quantity * $order->executed_price)
                    };

                    if ($diff != 0) {

                        $user = $order->user()->first();
                        if(!$user instanceof User){
                            throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
                        }

                        $marketExploded = explode('-', $order->market);

                        $user->setWallet($marketExploded[1], 2, $order->is_virtual)->chargeWallet(-$diff);

                        $user->setWallet($marketExploded[1], 1, $order->is_virtual)->chargeWallet($diff);

                    }
                }
            }
            DB::commit();
        }
        catch(\Throwable $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            throw $exception;
        }
    }
}
