<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Hermes\Market;

use App\Hermes\Hermes;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Market extends Hermes
{
    /**
     * @return Collection
     * @throws Exception
     * @throws GuzzleException
     */

    public function getMarkets(): Collection
    {
        return collect(json_decode($this->client()->get('markets')->getBody()->getContents())->markets)->values();
    }

    /**
     * @param string $market
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */

    public function getMarket(string $market): Collection
    {
        return collect(json_decode($this->client()->get('markets/' . $market)->getBody()->getContents())->market);
    }

    /**
     * @param string $market
     * @param bool $isActive
     * @param bool $isInternal
     * @param bool $isDirect
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */

    public function createMarket(string $market, bool $isActive, bool $isInternal, bool $isDirect, string $exchange): Collection
    {
        return collect(json_decode($this->client()->post('markets', [
            'json' => [
                'market' => $market,
                'is_active' => $isActive,
                'is_internal' => $isInternal,
                'is_direct' => $isDirect,
                'exchange'  => $exchange
            ]
        ])->getBody()->getContents())->market);
    }

    /**
     * @param string $market
     * @param bool $isActive
     * @param bool $isInternal
     * @param bool $isDirect
     * @return Collection|string
     * @throws GuzzleException
     * @throws Exception
     */
    public function updateMarket(
        string $market,
        bool $isActive,
        bool $isInternal,
        bool $isDirect,
        string $exchange
    ): Collection|string {
        try {
            $data = $this->client()->patch('markets/' . $market, [
                'json' => [
                    'is_active' => $isActive,
                    'is_internal' => $isInternal,
                    'is_direct' => $isDirect,
                    'exchange' => $exchange
                ]
            ]);

            $dataCollection = collect(
                json_decode(
                    $data->getBody()->getContents()
                )
            );

            if (isset($dataCollection['market'])) {
                return collect($dataCollection['market']);
            }
        } catch (GuzzleException $guzzleException) {
            throw new Exception($guzzleException->getMessage());
        } catch (\Throwable $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }

        throw new NotFoundHttpException('market Not Found');
    }

    /**
     * @param string $market
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */

    public function destroyMarket(string $market): Collection
    {
        return collect(json_decode($this->client()->delete('markets/' . $market)->getBody()->getContents()));
    }

    /**
     * @param string $market
     * @param string $timeframe
     * @return Collection
     * @throws GuzzleException
     */

    public function getCandles(string $market, string $timeframe): Collection
    {
        return collect(json_decode($this->client()->get('markets/candlestick-data/' . $market . '/' . $timeframe)->getBody()->getContents()));
    }

    /**
     * @param string $market
     * @return String
     */

    public static function setStaticExchange($market)
    {
        return str_contains($market, 'IRT') ? 'Nobitex' : 'Binance';
    }

}
