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
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Answer extends Model
{
    use HasFactory;

    /**
     * @var array|string[]
     */

    protected $fillable = ['content', 'attachment'];

    /**
     * @var string[]
     */

    protected $appends = ['guard_casted'];

    public function getGuardCastedAttribute()
    {
        $answerableType = $this->attributes['answerable_type'];

        return str_contains($answerableType, 'Admin') ? __('attributes.guards.admin') : __('attributes.guards.user');
    }

    /**
     * @return BelongsTo
     */

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * @return MorphTo
     */

    public function answerable(): MorphTo
    {
        return $this->morphTo();
    }
}
