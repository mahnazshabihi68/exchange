<?php

namespace App\Classes;

use App\Contracts\BlockchainExplorerContract;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class TronScan implements BlockchainExplorerContract
{
    /**
     * @var string
     */

    protected string $base;

    /**
     * @var string
     */

    protected string $blockchain;

    /**
     * @var string
     */

    protected string $address;

    public function __construct()
    {
        $this->base = 'https://apilist.tronscan.org/api/';
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
        $this->address = $address;

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

    public function getTransactions(): Collection
    {
        /**
         * Empty instance of transactions.
         */

        $transactions = collect();

        /**
         * Fetch native TRX transactions.
         */

        $nativeTRXTransactions = collect(json_decode($this->client()->get('transfer?address=' . $this->address)->getBody()->getContents())->data);

        $transactions->push($nativeTRXTransactions);

        /**
         * Fetch trc20 tokens.
         */

        $TRC20Transactions = collect(json_decode($this->client()->get('contract/events?address=' . $this->address)->getBody()->getContents())->data);

        $transactions->push($TRC20Transactions);

        /**
         * Return.
         */

        return $transactions->flatten()->reject(fn($tx) => !is_object($tx) || $tx->amount <= 0)->transform(function ($tx) {
            $tx->timestamp = $tx->timestamp / 1000;
            $tx->from = $tx->transferFromAddress;
            $tx->to = $tx->transferToAddress;
            $tx->quantity = (string)($tx->amount / pow(10, $tx->decimals ?? $tx->tokenInfo->tokenDecimal));
            $tx->hash = $tx->transactionHash;
            $tx->symbol = strtoupper($tx->tokenInfo->tokenName ?? 'USDT');
            $tx->confirmed = true;
            return $tx;
        });
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

            return !collect(json_decode($this->client()->get('account', [
                'query' => [
                    'address' => $this->address
                ]
            ])->getBody()->getContents()))->has('message');

        } catch (Exception|GuzzleException) {

            return false;

        }
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
}
