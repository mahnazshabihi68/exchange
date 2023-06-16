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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array|string[]
     */

    protected $fillable = ['hash', 'subject', 'department', 'content', 'attachment', 'status'];

    /**
     * @var string[]
     */

    protected $appends = ['status_casted'];

    /**
     * @return string
     */

    public function getStatusCastedAttribute(): string
    {
        return __('attributes.tickets.status.' . $this->attributes['status']);
    }

    /**
     * @return string
     */

    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    /**
     * @return HasMany
     */

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isClosed()
    {
        return intval($this->status) === 0;
    }
}
