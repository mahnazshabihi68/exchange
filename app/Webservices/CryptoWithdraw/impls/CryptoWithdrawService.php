<?php

namespace App\Webservices\CryptoWithdraw\impls;

use App\Webservices\CryptoWithdraw\interfaces\ICryptoWithdrawService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptoWithdrawService implements ICryptoWithdrawService
{
    /**
     * @var string
     */
    protected string $main;

    public function __construct()
    {
        $this->main = Config::get('app.crypto_withdraw_base_url');
    }

    /**
     * @param $data
     * @return mixed
     */
    public function withdraw($data): mixed
    {
        Log::channel('stderr')->info('BEFORE - GUZZLE', $data);
        $result = Http::post(
            $this->main . 'withdraw',
            $data
        );

        return $result->json();
    }
}
