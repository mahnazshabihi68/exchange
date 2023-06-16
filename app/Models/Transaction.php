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

class Transaction extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */

    protected $fillable = ['quantity', 'type', 'side', 'symbol_id'];

    protected $casts = [
        'quantity'  =>  'decimal:8'
    ];

    /**
     * @param $query
     * @param $side
     * @return mixed
     */

    public function scopeSide($query, $side): mixed
    {
        return $query->whereSide($side);
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
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */

    public function symbol(): BelongsTo
    {
        return $this->belongsTo(Symbol::class);
    }
}
