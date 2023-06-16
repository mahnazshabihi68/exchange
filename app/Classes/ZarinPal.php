<?php

namespace App\Classes;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class ZarinPal
{
    /**
     * @var string
     */

    public string $MerchantID;
    protected string $zarin_pal_base_url;

    public function __construct()
    {
        $this->MerchantID = Config::get('app.zarin_pal_merchant_id');

        $this->zarin_pal_base_url = Config::get('app.zarin_pal_base_url');
    }

    public function paymentRequest(float $amount, string $callBackUrl, string $clientRefId = null): Collection
    {
        $description = ('increase_credit');  // required
        $email = $this->user()->email ? $this->user()->email : '';
        $mobile = $this->user()->userProfile && $this->user()->userProfile->mobile ? $this->user()->userProfile->mobile : '';
        $metadata = $email && $mobile ? ["email" => $email, "mobile"=> $mobile] : array();

        $result = collect(json_decode($this->client()->post('request.json', [
            'json' => [
                'merchant_id' => $this->MerchantID, // Determine merchant code
                'amount' => $amount * 10, // Transaction amount
                'description' => $description, // Description of the transaction
                "metadata" => $metadata,
                'callback_url' => $callBackUrl, // Return address after payment
            ]
        ])->getBody()->getContents()));

        if ($result['data'] && $result['data']->code == 100) {
            return collect([
                'authority' => $result['data']->authority,
                'url'       => 'https://www.zarinpal.com/pg/StartPay/' . $result['data']->authority,
            ]);
        } else {
            throw new Exception("ERR :" . $result['errors']->message);
        }
    }


    public function paymentVerify(string $ref, float $amount)
    {
        $result = collect(json_decode($this->client()->post('verify.json', [
            'json' => [
                'merchant_id' => $this->MerchantID, // Determine merchant code
                'authority'   => $ref,
                'amount'     => $amount * 10
            ]
        ])->getBody()->getContents()));

        if ($result['data']->code == 100) return $result['data']->ref_id;

        else throw new Exception(__('messages.failed'));

    }

    /**
     * @return Authenticatable
     */

    private function user(): Authenticatable
    {
        return auth('user')->user();
    }

    private function client(): Client
    {
        return new Client([
            'base_uri' => $this->zarin_pal_base_url,
            'http_errors' => false,
            'headers' => [
                'accept' => 'application/json',
                'Content-type' => 'application/json'
            ],
        ]);
    }
}
