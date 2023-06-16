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

class AtiPay
{
    /**
     * @var string
     */

    protected string $base;

    /**
     * @var string
     */

    protected string $apiKey;

    public function __construct()
    {
        $this->base = 'https://mipg.atipay.net/v1/';

        $this->apiKey = config('settings.atipay_api_key');
    }

    /**
     * @param float $amount
     * @param string $callBackUrl
     * @param string $invoiceNumber
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */

    public function paymentRequest(float $amount, string $callBackUrl, string $invoiceNumber): Collection
    {
        $request = collect(json_decode($this->client()->post('get-token', [
            'json' => [
                'amount' => $amount * 10,
                'invoiceNumber' => $invoiceNumber,
                'redirectUrl' => $callBackUrl,
                'apiKey' => $this->apiKey
            ]
        ])->getBody()->getContents()));

        if (!$request->has('status') || !$request['status']) {

            throw new Exception(__('messages.failed'));

        }

        return collect([
            'token' => $request['token'],
            'url' => $this->base . 'redirect-to-gateway'
        ]);

    }

    /**
     * @return Client
     */

    private function client(): Client
    {
        return new Client([
            'base_uri' => $this->base,
            'http_errors' => false,
            'headers' => [
                'accept' => 'application/json',
                'Content-type' => 'application/json'
            ],
        ]);
    }

    /**
     * @param string $ref
     * @param float $amount
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */

    public function paymentVerify(string $ref, float $amount): Collection
    {
        $request = collect(json_decode($this->client()->post('verify-payment', [
            'json' => [
                'referenceNumber' => $ref,
                'apiKey' => $this->apiKey
            ],
        ])->getBody()->getContents()));

        if (!$request->has('amount') || $request['amount'] != $amount * 10) {

            throw new Exception(__('messages.failed'));

        }

        return $request;
    }
}
