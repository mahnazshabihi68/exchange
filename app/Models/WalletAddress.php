<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Models;

use App\Exceptions\Primary\NotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use SimpleSoftwareIO\QrCode\Generator;

class WalletAddress extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array|string[]
     */

    protected $fillable = ['title', 'address', 'is_active', 'allocated_at', 'private_key', 'last_used_at'];

    /**
     * @var string[]
     */

    protected $appends = ['is_active_casted', 'is_available', 'is_available_casted', 'will_unallocated_at', 'current_balance', 'QrCode'];

    /**
     * @var string[]
     */

    protected $casts = [
        'private_key' => 'encrypted:string',
        'allocated_at' => 'datetime',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * @return string
     */

    public function getCurrentBalanceAttribute(): string
    {
        /**
         * Todo: Due to rate limit and performance optimization, only active and in use wallets will considered.
         */

        if (!$this->attributes['is_active'] || !self::user()->exists()){

            $output = 0;

        }

        else {

            $output = '';

            foreach (self::getBalances() as $asset => $balance) {

                $output .= $balance . ' ' . $asset . ', ';

            }

        }

        return $output;
    }

    /**
     * @return string
     */

    public function getQrCodeAttribute(): string
    {
        return (new Generator())->size(250)->margin(3)->format('svg')->generate($this->attributes['address'])->toHtml();
    }

    /**
     * @return string
     */

    public function getIsActiveCastedAttribute(): string
    {
        return __('attributes.wallet_addresses.status.' . $this->attributes['is_active']);
    }

    /**
     * @return Carbon|null
     */

    public function getWillUnallocatedAtAttribute(): ?Carbon
    {
        if (!self::user()->exists()) {

            return null;

        }

        /**
         * Calculate unallocating time.
         */

        return Carbon::parse($this->attributes['last_used_at'])->addHours(config('settings.deallocating_wallets_after_hours'));

    }

    /**
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return bool
     */

    public function getIsAvailableAttribute(): bool
    {
        return self::isAvailable();
    }

    /**
     * @return bool
     */

    public function isAvailable(): bool
    {
        return !self::user()->exists();
    }

    /**
     * @return string
     */

    public function getIsAvailableCastedAttribute(): string
    {
        return __('attributes.wallet_addresses.availability.' . (int)self::isAvailable());
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeAvailable($query): mixed
    {
        return $query->where('user_id', null);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsNotAvailable($query): mixed
    {
        return $query->where('user_id', '!=', null);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsActive($query): mixed
    {
        return $query->whereIsActive(true);
    }

    /**
     * @return void]
     */

    public function deallocate(): void
    {
        $this->setAttribute('allocated_at', null);
        $this->setAttribute('user_id', null);
        $this->save();
    }

    /**
     * @param  bool  $filteredForDeposit
     * @return Collection|null
     * @throws NotFoundException
     */

    public function getTransactions(bool $filteredForDeposit = false): null|Collection
    {
        $blockchain = self::blockchain()->first();
        if(!$blockchain instanceof Blockchain){
            throw new NotFoundException(NotFoundException::BLOCKCHAIN_NOT_FOUND);
        }

         $txs = $blockchain?->getExplorer()
            ->setBlockchain($blockchain->title)
            ->setAddress($this->attributes['address'])
            ->getTransactions();

         if ($filteredForDeposit){

             $receivableSymbols = Symbol::query()->isDepositable()->get();

             $txs = $txs->reject(function ($tx) use ($receivableSymbols){

                 return

                     /**
                      * Transaction symbol should have been set as accepted for deposits by admin.
                      */

                     !$receivableSymbols->contains('title', $tx->symbol)

                     /**
                      * We must not have any deposit with this transaction hash.
                      */

                     || Deposit::whereRef($tx->hash)->exists()

                     /**
                      * transaction should be incoming.
                      */

                     || (isset($tx->to) && ($tx->to != $this->attributes['address']))
                     || (isset($tx->tx_input_n) && $tx->tx_input_n != -1)

                     /**
                      * Transaction should get submitted after @allocated_at.
                      */

                     || $tx->timestamp < strtotime($this->attributes['allocated_at'])

                     /**
                      * Transaction must meet the confirmations count policy which has been set by admin.
                      */

                     || ((isset($tx->confirmations) && $tx->confirmations < self::blockchain()->first()->deposit_min_needed_confirmations))
                     || (isset($tx->confirmed) && $tx->confirmed == false);
             });

         }

         return $txs;
    }

    /**
     * @return BelongsTo
     */

    public function blockchain(): BelongsTo
    {
        return $this->belongsTo(Blockchain::class);
    }

    /**
     * @return Collection|null
     * @throws NotFoundException
     */

    public function getBalances(): null|Collection
    {
        $blockchain = self::blockchain()->first();
        if(!$blockchain instanceof Blockchain){
            throw new NotFoundException(NotFoundException::BLOCKCHAIN_NOT_FOUND);
        }

        return $blockchain
            ?->getExplorer()
            ->setBlockchain($blockchain->title)
            ->setAddress($this->attributes['address'])
            ->getBalances();
    }
}
