<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Jobs;

use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Models\Order;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderUpdate implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('orders-update-queue');
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function handle():void
    {
        Order::has('user')->isActive()->chunkById(
        /**
         * @throws \JsonException
         */ /**
         * @throws \JsonException
         */ 10, function ($orders) {

            try {

                foreach ($orders as $order) {

                    $order = Order::find($order->id);
                    if(!$order instanceof Order){
                        throw new NotFoundException(NotFoundException::ORDER_NOT_FOUND);
                    }

                    match ($order->status) {
                        'PENDING_CANCELED' => $order->cancelOrder(),
                        'NEW', 'PARTIALLY_FILLED' => $order->updateOrder()
                    };

                }

            } catch (Exception|GuzzleException $exception) {
                Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            }
        });
    }
}
