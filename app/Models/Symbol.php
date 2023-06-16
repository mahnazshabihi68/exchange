<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Symbol extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */

    protected $fillable = [
        'title',
        'name_en',
        'name_fa',
        'picture',
        'is_withdrawable',
        'is_depositable',
        'min_withdrawable_quantity',
        'max_withdrawable_quantity',
        'precision'
    ];

    protected $casts = [
        'is_withdrawable' => 'boolean',
        'is_depositable' => 'boolean',
        'min_withdrawable_quantity' => 'decimal:8',
        'max_withdrawable_quantity' => 'decimal:8',
        'precision' => 'integer'
    ];

    /**
     * @return string
     */

    public function getPictureAttribute(): string
    {
        return config('filesystems.disks.public.url') . '/'. $this->attributes['picture'];
    }

    /**
     * @param $query
     * @param string $title
     * @return mixed
     */

    public function scopeTitle($query, string $title): mixed
    {
        return $query->whereTitle($title);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsDepositable($query): mixed
    {
        return $query->where('is_depositable', true);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsWithdrawable($query): mixed
    {
        return $query->where('is_withdrawable', true);
    }

    /**
     * @return BelongsToMany
     */

    public function blockchains(): BelongsToMany
    {
        return $this->belongsToMany(Blockchain::class)->withPivot('transfer_fee');
    }

    /**
     * @return HasMany
     */

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * @return HasMany
     */

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany
     */

    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class);
    }

    /**
     * @return HasMany
     */

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }
}
