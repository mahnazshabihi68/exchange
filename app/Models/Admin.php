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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\Contracts\HasApiTokens as ContractsHasApiTokens;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements ContractsHasApiTokens
{
    use HasFactory, HasRoles, HasApiTokens;

    /**
     * @var string
     */

    protected string $guard_name = 'admin';

    /**
     * @var array|string[]
     */

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'email',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return MorphMany
     */

    public function answers(): MorphMany
    {
        return $this->morphMany(Answer::class, 'answerable');
    }

    /**
     * @return MorphMany
     */

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
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

    /**
     * @return HasMany
     */

    public function manualDeposits(): HasMany
    {
        return $this->hasMany(ManualDeposit::class);
    }
}
