<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Traits\Exchange;

use App\Hermes\Market\Market;
use App\Hermes\Order\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

trait MarketTrait
{

    /**
     * @return Collection
     */

    public function getMarkets(): Collection
    {
        return collect(Redis::connection('markets')->keys('markets:*'))->transform(fn($market) => [Str::after($market, config('database.redis.options.prefix') . 'markets:') => Redis::get(Str::after($market, config('database.redis.options.prefix')))]);
    }

    /**
     * @param string $srcSymbol
     * @param string $dstSymbol
     * @return float
     */

    public function getConvertRatio(string $srcSymbol, string $dstSymbol): float
    {

        /**
         * Check if src and dst are equal together or not.
         */

        if ($srcSymbol === $dstSymbol) {

            return 1;

        }

        /**
         * Seek for direct market.
         */

        if ($this->getMarket($srcSymbol . '-' . $dstSymbol)->isNotEmpty()) {

            return $this->getMarketPrice($srcSymbol . '-' . $dstSymbol);

        }

        /**
         * Direct market does not found, check if we have mirrored market exists or not.
         */

        if ($this->getMarket($dstSymbol . '-' . $srcSymbol)->isNotEmpty()) {

            return 1 / $this->getMarketPrice($dstSymbol . '-' . $srcSymbol);

        }

        /**
         * Mirrored market does not found neither, so we have to keep going forward with pivoted markets.
         */

        $acceptedPivotAssets = collect(['USDT', 'BTC', 'IRT', 'ETH']);

        $price = null;

        while ($acceptedPivotAssets->count() > 0 && is_null($price)) {

            $possiblePivotAsset = $acceptedPivotAssets->shift();

            $pivotMarket = $this->getMarket($srcSymbol . '-' . $possiblePivotAsset);

            $pivotMarketToDst = $this->getMarket($possiblePivotAsset . '-' . $dstSymbol);

            if ($pivotMarket->isNotEmpty() && $pivotMarketToDst->isNotEmpty()) {

                return $this->getMarketPrice($srcSymbol . '-' . $possiblePivotAsset) * $this->getMarketPrice($possiblePivotAsset . '-' . $dstSymbol);

            }

        }

        return 0;
    }

    /**
     * @param string $market
     * @return Collection
     */

    public function getMarket(string $market): Collection
    {
        return collect(Redis::connection('markets')->get('markets:' . $market))->transform(fn($price) => [$market => $price]);
    }

    /**
     * @param string $market
     * @return float|null
     */

    public function getMarketPrice(string $market): float|null
    {
        return $this->getMarket($market)?->first()[$market];
    }

    /**
     * @return Market
     */

    public function hermesMarket(): Market
    {
        return new Market();
    }

    /**
     * @return Order
     */

    public function hermesOrder(): Order
    {
        return new Order();
    }
}
