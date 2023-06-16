<?php

namespace App\Console\Commands;

use App\Classes\BlockCypher;
use App\Classes\EtherScan;
use App\Classes\TronScan;
use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Models\Symbol;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletAddress;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BlockchainExploreCommand extends Command
{

    protected $signature = 'blockchains:explore';

    protected $description = 'Explores the accepted blockchains and trace the receives.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function handle():void
    {
        WalletAddress::with(['user', 'blockchain'])->isActive()->isNotAvailable()->chunkById(3, function ($walletAddresses){

            foreach ($walletAddresses as $walletAddress){

                DB::beginTransaction();
                try {

                    /**
                     * Fetch user and blockchain.
                     */

                    $user = $walletAddress->user()->first();
                    if(!$user instanceof User){
                        throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
                    }

                    /**
                     * Fetch and submit transactions.
                     */

                    $walletAddress->getTransactions(true)->each(function ($tx) use ($user){

                        /**
                         * Define symbol.
                         */

                        $symbol = Symbol::title($tx->symbol)?->firstOrFail();

                        /**
                         * Create new deposit for user.
                         */

                        $user->deposits()->create([
                            'quantity' => $tx->quantity,
                            'symbol_id' => $symbol->id,
                            'ref' => $tx->hash,
                            'status' => true
                        ]);

                        /**
                         * Charge user's wallet.
                         */

                        $user->setWallet($symbol->id, 1, false)->chargeWallet($tx->quantity);

                    });

                    /**
                     * Handle deallocating wallets.
                     */

                    if (Carbon::parse($walletAddress->will_unallocated_at)->lessThanOrEqualTo(now())) {

                        $walletAddress->deallocate();

                    }
                    DB::commit();
                    /**
                     * Sleep.
                     */

                    sleep(1);

                } catch (Exception $exception){
                    DB::rollback();
                    Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
                    $this->error($exception->getMessage());

                    continue;
                }
            }
        });

        $this->info($this->signature . ' has been lunched successfully at '. now());
    }
}
