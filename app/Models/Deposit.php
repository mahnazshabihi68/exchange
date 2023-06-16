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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Deposit extends Model
{
    use HasFactory;

    /**
     * @var array|string[]
     */

    protected $fillable = ['internal_deposit_id', 'symbol_id', 'gateway', 'quantity', 'ref', 'status'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($deposit) {
            $deposit->internal_deposit_id = Str::orderedUuid()->toString();
        });
    }

    /**
     * @var string[]
     */

    protected $with = [
        'symbol'
    ];

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsVerified($query): mixed
    {
        return $query->whereStatus(true);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsNotVerified($query): mixed
    {
        return $query->whereStatus(false);
    }

    /**
     * @return HasOne
     */

    public function paymentTransaction(): HasOne
    {
        return $this->hasOne(PaymentTransaction::class);
    }

    /**
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * @return BelongsTo
     */

    public function symbol(): BelongsTo
    {
        return $this->belongsTo(Symbol::class);
    }
}
