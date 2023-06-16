<?php

namespace App\Observers;

use App\Exceptions\Primary\NotFoundException;
use App\Models\Order;
use App\Models\Trade;

class TradeObserver
{
    /**
     * @throws NotFoundException
     */
    public function created(Trade $trade)
    {
        $order = $trade->order()->first();
        if (!$order instanceof Order) {
            throw new NotFoundException(NotFoundException::ORDER_NOT_FOUND);
        }

        $symbols = explode('-', $order->market);

        $order->user()->first()
            ->notifications()
            ->create([
                'title' => __('messages.tradeCreated', [
                    'side' => $order->side_casted,
                    'srcSymbol' => $symbols[0],
                    'dstSymbol' => $symbols[1],
                    'price' => $trade->price,
                    'quantity' => $trade->quantity,
                ]),
            ]);
    }
}
