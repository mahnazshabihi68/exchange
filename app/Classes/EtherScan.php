<?php

namespace App\Classes;

use App\Contracts\BlockchainExplorerContract;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class EtherScan implements BlockchainExplorerContract
{
    /**
     * @var string
     */

    protected string $base;

    /**
     * @var string
     */

    protected string $apiKey;

    /**
     * @var string
     */

    protected string $blockchain;

    /**
     * @var string
     */

    protected string $address;

    /**
     * EtherScan constructor.
     */

    public function __construct()
    {
        $this->base = 'https://api.etherscan.io/api/';

        $this->apiKey = config('settings.etherscan_api_key');
    }

    /**
     * @return string
     */

    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return $this
     */

    public function setAddress(string $address): static
    {
        $this->address = strtolower($address);

        return $this;
    }

    /**
     * @return string
     */

    public function getBlockchain(): string
    {
        return $this->blockchain;
    }

    /**
     * @param string $blockchain
     * @return $this
     */

    public function setBlockchain(string $blockchain): static
    {
        $this->blockchain = $blockchain;

        return $this;
    }

    /**
     * @return Collection
     * @throws GuzzleException
     */

    public function getBalances(): Collection
    {
        $output = [];

        foreach (self::getTransactions() as $transaction) {

            $qty = ($transaction->to == $this->address) ? $transaction->quantity : -($transaction->quantity);

            isset($output[$transaction->symbol]) ? $output[$transaction->symbol] += $qty : $output[$transaction->symbol] = $qty;

        }

        return collect($output)->reject(fn($qty) => $qty <= 0)->transform(fn($qty) => number_format($qty, 8, '.', ''));
    }

    /**
     * @return Collection
     * @throws GuzzleException
     */

    public function getTransactions(): Collection
    {
        /**
         * Empty instance of transactions.
         */

        $transactions = collect();

        /**
         * Collect native ETH transactions.
         */

        $nativeETHTransactions = collect(json_decode($this->client()->post('', [
            'query' => [
                'module' => 'account',
                'action' => 'txlist',
                'address' => $this->address,
                'apikey' => $this->apiKey
            ],
        ])->getBody()->getContents()));

        if ($nativeETHTransactions['status'] != 0) {

            $transactions->push($nativeETHTransactions);

        }

        /**
         * Collect token transactions.
         */

        $ERC20Transactions = collect(json_decode($this->client()->post('', [
            'query' => [
                'module' => 'account',
                'action' => 'tokentx',
                'address' => $this->address,
                'apikey' => $this->apiKey
            ],
        ])->getBody()->getContents()));

        if ($ERC20Transactions['status'] != 0) {

            $transactions->push($ERC20Transactions);

        }

        /**
         * Process and output.
         */

        return $transactions
            ->flatten()
            ->reject(fn($tx) => !is_object($tx) || $tx->value <= 0)
            ->transform(function ($tx) {
                $tx->timestamp = $tx->timeStamp;
                $tx->symbol = $tx->tokenSymbol ?? 'ETH';
                $tx->quantity = (string)($tx->value / pow(10, $tx->tokenDecimal ?? 18));
                return $tx;
            })->values();

    }

    /**
     * @return Client
     */

    private function client(): Client
    {
        return new Client([
            'base_uri' => $this->base,
            'http_errors' => false,
        ]);
    }

    /**
     * @return bool
     */

    public function addressIsValid(): bool
    {
        try {

            return collect(json_decode($this->client()->get('', [
                    'query' => [
                        'module' => 'account',
                        'action' => 'balance',
                        'address' => $this->address,
                        'apikey' => $this->apiKey
                    ],
                ])->getBody()->getContents()))['status'] != 0;

        } catch (Exception|GuzzleException) {

            return false;

        }
    }
}
