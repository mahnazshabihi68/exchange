<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Models;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualDeposit extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */

    protected $fillable = ['picture', 'status', 'quantity', 'currency', 'ref'];

    /**
     * @var string[]
     */

    protected $appends = ['status_casted'];

    /**
     * @return array|Application|Translator|string|null
     */

    public function getStatusCastedAttribute()
    {
        return __('attributes.manualDeposits.status.' . $this->attributes['status']);
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
}
