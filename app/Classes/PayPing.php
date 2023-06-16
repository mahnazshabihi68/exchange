<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Classes;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class PayPing
{
    /**
     * @var string
     */

    protected string $base;

    /**
     * @PayPing Class Constructor.
     */

    public function __construct()
    {
        $this->base = 'https://api.payping.ir/v2/';
    }

    /**
     * @param float $amount
     * @param string $callBackUrl
     * @param string|null $clientRefId
     * @return Collection
     * @throws GuzzleException
     */

    public function paymentRequest(float $amount, string $callBackUrl, string $clientRefId = null): Collection
    {
        /**
         * Make payment request.
         */

        $paymentRequest = $this->client(true)->post('pay', [
            'json' => [
                'amount' => $amount,
                'returnUrl' => $callBackUrl,
                'clientRefId' => $clientRefId
            ]
        ]);

        /**
         * Process the payment request body.
         */

        $paymentRequestCode = json_decode($paymentRequest->getBody()->getContents())->code;

        /**
         * Return collection.
         */

        return collect([
            'payment-ref' => $paymentRequestCode,
            'payment-url' => $this->base . '/pay/gotoipg/' . $paymentRequestCode
        ]);
    }

    /**
     * @param bool $isAuthenticated
     * @return Client
     */

    private function client(bool $isAuthenticated = false): Client
    {
        $headers = [
            'accept' => 'application/json',
        ];

        if ($isAuthenticated) {

            $headers['Authorization'] = 'Bearer ' . config('settings.payping_api_key');

        }

        return new Client([
            'base_uri' => $this->base,
            'headers' => $headers,
            'http_errors' => false
        ]);
    }

    /**
     * @param string $ref
     * @param float $amount
     * @return Collection
     * @throws Exception|GuzzleException
     */

    public function paymentVerify(string $ref, float $amount): Collection
    {
        return collect(json_decode($this->client(true)->post('pay/verify', [
            'json' => [
                'amount' => $amount,
                'refId' => $ref
            ]
        ])->getBody()->getContents()));
    }
}
