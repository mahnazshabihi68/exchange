<?php

namespace App\Classes;

use App\Contracts\BlockchainExplorerContract;
use App\Helpers\Logger;
use App\Helpers\Util;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class BlockCypher implements BlockchainExplorerContract
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
        $this->base = 'https://api.blockcypher.com/v1/';
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
        $this->blockchain = strtolower($blockchain);

        $this->base .= $this->blockchain . '/main/';

        return $this;
    }

    /**
     * @return Collection
     * @throws GuzzleException
     */

    public function getTransactions(): Collection
    {
        return collect(json_decode($this->client()->get('addrs/' . $this->address)->getBody()->getContents()))->only(['txrefs', 'unconfirmed_txrefs'])->flatten()->reject(fn($tx) => !is_object($tx) || $tx->value <= 0)
            ->transform(function ($tx) {
                $tx->hash = $tx->tx_hash;
                $tx->quantity = (string)($tx->value / pow(10, 8));
                $tx->symbol = strtoupper($this->blockchain);
                $tx->timestamp = Carbon::parse($tx->confirmed ?? $tx->received)->unix();
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

            return !collect(json_decode($this->client()->get('addrs/' . $this->address . '/balance')->getBody()->getContents()))->has('error');

        } catch (Exception|GuzzleException) {

            return false;

        }
    }

    /**
     * @return Collection
     * @throws \JsonException
     */
    public function getBalances(): Collection
    {
        try {
            $balance = json_decode($this->client()->get('addrs/' . $this->address . '/balance')->getBody()->getContents())?->final_balance / pow(10, 8);

        } catch (\Throwable $exception) {
            Logger::error($exception->getMessage());
            $balance = -1;
        }

        return collect([
            strtoupper($this->getBlockchain()) => number_format($balance, 8, '.', '')
        ]);
    }
}
