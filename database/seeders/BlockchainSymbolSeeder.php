<?php

namespace Database\Seeders;

use App\Classes\BlockCypher;
use App\Classes\EtherScan;
use App\Classes\TronScan;
use App\Models\Blockchain;
use App\Models\Symbol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class BlockchainSymbolSeeder extends Seeder
{

    public function run()
    {
        /**
         * Create blockchains.
         */

        foreach ($this->getAllBlockchains() as $blockchain) {

            Blockchain::create($blockchain);

        }

        /**
         * Create symbols.
         */

        $symbols = $this->getAllSymbols()->toArray();

        foreach ($symbols as $symbol) {

            $symbol = Symbol::create(collect($symbol)->toArray());

            $blockchains = match ($symbol['title']) {
                'BTC' => [
                    [
                        'blockchain' => 'BTC',
                        'transfer_fee' => 0.00035
                    ]
                ],
                'ETH' => [
                    [
                        'blockchain' => 'ETH',
                        'transfer_fee' => 0.005
                    ]
                ],
                'USDT' => [
                    [
                        'blockchain' => 'ETH',
                        'transfer_fee' => 60
                    ],
                    [
                        'blockchain' => 'TRX',
                        'transfer_fee' => 0.8
                    ]
                ],
                'TRX' => [
                    [
                        'blockchain' => 'TRX',
                        'transfer_fee' => 3
                    ]
                ],
                'LTC' => [
                    [
                        'blockchain' => 'LTC',
                        'transfer_fee' => 0.001
                    ]
                ],
                default => []
            };

            foreach ($blockchains as $blockchain) {

                $symbol->blockchains()->attach(Blockchain::title($blockchain['blockchain'])->first(), ['transfer_fee' => $blockchain['transfer_fee']]);

            }
        }
    }

    /**
     * @return Collection
     */

    private function getAllBlockchains(): Collection
    {
        return collect([
            [
                'title' => 'BTC',
                'name_fa' => 'بیت کوین',
                'name_en' => 'Bitcoin',
                'picture'   =>  'symbols/BTC.png',
                'explorer'  =>  get_class(new BlockCypher())
            ],
            [
                'title' => 'ETH',
                'name_fa' => 'اتریوم',
                'name_en' => 'Ethereum',
                'picture'   =>  'symbols/ETH.png',
                'explorer'  =>  get_class(new EtherScan())
            ],
            [
                'title' => 'TRX',
                'name_fa' => 'ترون',
                'name_en' => 'Tron',
                'picture'   =>  'symbols/TRX.png',
                'explorer'  =>  get_class(new TronScan())
            ],
            [
                'title' => 'LTC',
                'name_fa' => 'لایت کوین',
                'name_en' => 'Litecoin',
                'picture'   =>  'symbols/LTC.png',
                'explorer'  =>  get_class(new BlockCypher())
            ],
        ]);
    }

    /**
     * @return Collection
     */

    private function getAllSymbols(): Collection
    {
        return collect([
            [
                'title' => 'BTC',
                'name_fa' => 'بیت کوین',
                'name_en' => 'Bitcoin',
                'is_withdrawable' => true,
                'is_depositable' => true,
                'min_withdrawable_quantity' => 0.001,
                'max_withdrawable_quantity' => 100,
                'picture'   =>  'symbols/BTC.png',
            ],
            [
                'title' => 'IRT',
                'name_fa' => 'تومان',
                'name_en' => 'IRT',
                'is_withdrawable' => true,
                'is_depositable' => true,
                'min_withdrawable_quantity' => 1000000,
                'max_withdrawable_quantity' => 50000000,
                'picture'   =>  'symbols/IRT.png',
            ],
            [
                'title' => 'ETH',
                'name_fa' => 'اتریوم',
                'name_en' => 'Ethereum',
                'is_withdrawable' => true,
                'is_depositable' => true,
                'min_withdrawable_quantity' => 0.01,
                'max_withdrawable_quantity' => 250,
                'picture'   =>  'symbols/ETH.png',
            ],
            [
                'title' => 'USDT',
                'name_fa' => 'تتر',
                'name_en' => 'Tether',
                'is_withdrawable' => true,
                'is_depositable' => true,
                'min_withdrawable_quantity' => 10,
                'max_withdrawable_quantity' => 5000,
                'picture'   =>  'symbols/USDT.png',
            ],
            [
                'title' => 'TRX',
                'name_fa' => 'ترون',
                'name_en' => 'Tron',
                'is_withdrawable' => true,
                'is_depositable' => true,
                'min_withdrawable_quantity' => 10,
                'max_withdrawable_quantity' => 5000,
                'picture'   =>  'symbols/TRX.png',
            ],
            [
                'title' => 'LTC',
                'name_fa' => 'لایت کوین',
                'name_en' => 'Litecoin',
                'is_withdrawable' => true,
                'is_depositable' => true,
                'min_withdrawable_quantity' => 10,
                'max_withdrawable_quantity' => 5000,
                'picture'   =>  'symbols/LTC.png',
            ],
        ]);
    }
}
