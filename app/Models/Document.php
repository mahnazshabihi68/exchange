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
use Spatie\Permission\Models\Permission;

class Document extends Model
{
    use HasFactory;

    /**
     * @var array|string[]
     */

    protected $fillable = ['title_fa', 'title_en', 'description_fa', 'description_en', 'requires_approval', 'status', 'example'];

    /**
     * @param $query
     * @return mixed
     */

    public function scopeActive($query)
    {
        return $query->whereStatus(true);
    }

    /**
     * @return BelongsToMany
     */

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['document', 'status'])->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
