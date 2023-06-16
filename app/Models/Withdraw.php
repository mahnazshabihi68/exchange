<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Withdraw extends Model
{
    use HasFactory;

    /**
     * @var array|string[]
     */

    protected $fillable = ['hash', 'symbol_id', 'ref', 'quantity', 'wage_quantity', 'destination', 'status', 'reject_reason', 'error_withdraw_transaction'];

    /**
     * @var string[]
     */

    protected $appends = ['status_casted', 'final_quantity'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->hash = (new Controller())->tokenGenerator(6, 'withdraws', 'hash');
        });
    }

    /**
     * @var string[]
     */

    protected $with = [
        'symbol'
    ];

    /**
     * @return float
     */

    public function getFinalQuantityAttribute(): float
    {
        return $this->attributes['quantity'];
    }

    /**
     * @return string
     */

    public function getStatusCastedAttribute(): string
    {
        return __('attributes.withdraws.' . $this->attributes['status']);
    }

    /**
     * @return string
     */

    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeComplete($query)
    {
        return $query->whereStatus(2);
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

    /**
     * @return hasMany
     */

    public function callbackWithdrawFiats(): hasMany
    {
        return $this->hasMany(CallbackWithdrawFiat::class);
    }
}
