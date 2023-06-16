<?php

namespace App\Models;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Traits\Exchange\MarketTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Accountancy extends Model
{
    use HasFactory, MarketTrait;

    /**
     * @var string[]
     */

    protected $fillable = [
        'accountancy',
        'IRT_summation',
        'USDT_summation'
    ];

    /**
     * @var string[]
     */

    protected $casts = [
        'accountancy' => 'array',
        'IRT_summation' => 'decimal:0',
        'USDT_summation' => 'decimal:2'
    ];

    /**
     * @return Collection
     */

    public function getTradeWagesAccountancy(): Collection
    {
        return User::find(config('settings.trade_wage_receiver_user_id'))
            ->wallets()
            ->isReal()
            ->get()
            ->groupBy('symbol.title')
            ->map(fn($wallet) => $wallet->sum('quantity'));
    }

    /**
     * @throws GuzzleException
     */

    public function getHermesAccountancy(): Collection
    {
        return (new \App\Hermes\Accountancy\Accountancy())->getAccountancy();
    }

    /**
     * @return Collection
     * @throws \JsonException
     */

    public function getWalletAddressesAccountancy(): Collection
    {
        $walletAddresses = WalletAddress::isActive()->latest()->get();

        $output = [];

        foreach ($walletAddresses as $walletAddress) {
            try {
                $walletBalances = $walletAddress->getBalances();

                foreach ($walletBalances as $item => $value) {
                    isset($output[$item]) ? $output[$item] += $value : $output[$item] = $value;
                }
            } catch (\Exception|GuzzleException $exception) {
                Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            }
        }

        return collect($output);
    }

    /**
     * @return Collection
     */

    public function getWalletsAccountancy(): Collection
    {
        return Wallet::isReal()
            ->get()
            ->groupBy('symbol.title')
            ->map(fn($wallet) => $wallet->sum('quantity'));
    }

    /**
     * @return Collection
     * @throws \JsonException
     */

    public function getCumulativeAccountancy(): Collection
    {
        try {
            $data = [
                'hermes' => $this->getHermesAccountancy()->toArray(),
                'wallet-addresses' => $this->getWalletAddressesAccountancy()->toArray(),
                'user-wallets' => $this->getWalletsAccountancy()->toArray(),
                'trade-wages' => $this->getTradeWagesAccountancy()->toArray(),
            ];

            $output = [];

            foreach ($data as $ref => $assets) {
                foreach ($assets as $asset => $value) {
                    foreach (array_keys($data) as $key) {
                        if (isset($data[$key][$asset])) {
                            $output[$asset][$key] = $data[$key][$asset];
                        } else {
                            $output[$asset][$key] = 0;
                        }
                    }

                    /**
                     * Current approach: Summation of User wallets And wallet Addresses (hermes has been excluded).
                     * Wallet-addresses and user-wallets are always GTE 0.
                     * Hermes might be positive or negative otherwise (based on buy/sell created trades.).
                     */

                    /**
                     * Skip Hermes in summation.
                     */

                    if ($ref === 'hermes') {
                        continue;
                    }

                    /**
                     * User wallets are lt 0 cause its system's debt.
                     */

                    if ($ref === 'user-wallets') {
                        $value = -($value);
                    }

                    /**
                     * Final summation.
                     */

                    if (isset($output[$asset]['sum'])) {
                        $output[$asset]['sum'] += $value;
                    } else {
                        $output[$asset]['sum'] = $value;
                    }
                }
            }

            return collect($output);
        } catch (\Exception|GuzzleException $exception) {
            Logger::error($exception->getMessage(),Util::jsonEncodeUnicode($exception->getTrace()));

            return collect();
        }
    }

    /**
     * @return Accountancy
     */

    public function createCumulativeAccountancy(): Accountancy
    {
        /**
         * Get latest cumulative accountancy.
         */

        $accountancy = $this->getCumulativeAccountancy();

        /**
         * Calculate IRT and USDT summation.
         */

        $USDT_summation = 0;

        foreach ($accountancy as $asset => $data) {
            $USDT_summation += $data['sum'] * $this->getConvertRatio($asset, 'USDT');
        }

        $IRT_summation = $USDT_summation * $this->getConvertRatio('USDT', 'IRT');

        /**
         * Store in database.
         */

        return self::create([
            'accountancy' => $accountancy,
            'USDT_summation' => $USDT_summation,
            'IRT_summation' => $IRT_summation
        ]);
    }
}
