<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Hermes\Order;

use App\Hermes\Hermes;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class Order extends Hermes
{
    /**
     * @return Collection
     * @throws GuzzleException
     */

    public function getOrders(): Collection
    {
        return collect(json_decode($this->client()->get('orders')->getBody()->getContents())->orders)->values();
    }

    /**
     * @param string $orderId
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */

    public function getOrder(string $orderId): Collection
    {
        return collect(json_decode($this->client()->get('orders/' . $orderId)->getBody()->getContents())->order);
    }

    /**
     * @param string $type
     * @param string $side
     * @param float $quantity
     * @param string $market
     * @param float|null $price
     * @param float|null $stop_price
     * @param bool $is_virtual
     * @return Collection
     * @throws Exception
     * @throws GuzzleException
     */

    public function createOrder(string $type, string $side, float $quantity, string $market, float $price = null, float $stop_price = null, bool $is_virtual = false): Collection
    {
        /**
         * Let's begin with preparing our order array of data.
         */

        $body = [
            'type' => $type,
            'original_quantity' => $quantity,
            'market' => $market,
            'side' => $side,
            'is_virtual' => $is_virtual
        ];

        if ($price) {

            $body['original_price'] = $price;

        }

        if ($stop_price) {

            $body['stop_price'] = $stop_price;

        }

        /**
         * Make request.
         */

        $request = $this->client()->post('orders', [
            'json' => $body,
        ]);

        /**
         * Error handling.
         */

        if ($request->getStatusCode() != 201){

            throw new Exception(json_decode($request->getBody()->getContents())->message);

        }

        /**
         * Return object of order.
         */

        return collect(json_decode($request->getBody()->getContents())->order);
    }

    /**
     * @param string $orderId
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */

    public function cancelOrder(string $orderId): Collection
    {
        $request = $this->client()->delete('orders/' . $orderId);

        /**
         * Error handling.
         */

        if ($request->getStatusCode() != 200){

            throw new Exception(json_decode($request->getBody()->getContents())->message);

        }

        return collect(json_decode($request->getBody()->getContents())->order);
    }
}
