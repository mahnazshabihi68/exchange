<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Console\Commands;

use App\Traits\Exchange\MarketTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class MarketUpdateCommand extends Command
{
    use MarketTrait;

    protected $signature = 'markets:update';

    protected $description = 'Updates markets and set them in Redis.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {

            cache()->clear();

            $markets = $this->hermesMarket()->getMarkets()->reject(fn($market) => !$market->is_active);

            Redis::connection('markets')->pipeline(function ($pipe) use ($markets) {

                foreach ($markets as $market) {

                    $pipe->set('markets:' . $market->market, $market->exchange_price ?? 0);

                }

            });

            $this->info($this->signature . ' has been lunched successfully at ' . now());

        } catch (Exception|GuzzleException $exception) {

            $this->error($this->signature . ' has been faced problem.' . PHP_EOL . 'Exception:' . $exception->getMessage());

        }
    }
}
