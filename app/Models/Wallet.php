<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Models;

use App\Traits\Exchange\MarketTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory, MarketTrait;

    /**
     * @var array|string[]
     */

    protected $fillable = ['quantity', 'type', 'is_virtual', 'user_id', 'symbol_id'];

    /**
     * @var array|string[]
     */

    protected $appends = ['type_casted', 'quantity_USDT'];

    protected $with = ['symbol'];

    /**
     * @var string[]
     */

    protected $casts = [
        'quantity' => 'decimal:8',
        'type' => 'integer',
        'quantity_USDT' => 'decimal:2',
        'is_virtual' => 'boolean'
    ];

    /**
     * @return string
     */

    public function getTypeCastedAttribute(): string
    {
        return __('attributes.wallets.' . $this->attributes['type']);
    }

    /**
     * @return string
     */

    public function getQuantityUSDTAttribute(): string
    {
        return ($this->attributes['quantity'] * $this->getConvertRatio($this->symbol()->first()->title, 'USDT'));
    }

    /**
     * @return BelongsTo
     */

    public function symbol(): BelongsTo
    {
        return $this->belongsTo(Symbol::class);
    }

    /**
     * @param $query
     * @param $type
     * @return mixed
     */

    public function scopeType($query, $type): mixed
    {
        return $query->whereType($type);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsVirtual($query): mixed
    {
        return $query->where('is_virtual', true);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsReal($query): mixed
    {
        return $query->where('is_virtual', false);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsAvailable($query): mixed
    {
        return $query->whereType(1);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsFrozen($query): mixed
    {
        return $query->whereType(2);
    }

    /**
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $query
     * @param $symbol
     * @return mixed
     */

    public function scopeSymbol($query, $symbol): mixed
    {
        return $query->whereHas('symbol', fn($q) => $q->whereTitle($symbol));
    }

    /**
     * @param $query
     * @param $symbols
     * @return mixed
     */

    public function scopeSymbols($query, $symbols): mixed
    {
        return $query->whereHas('symbol', fn($q) => $q->whereIn('title', $symbols));
    }
}
