<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Models;

use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Contracts\HasApiTokens as ContractsHasApiTokens;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements ContractsHasApiTokens
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    public Wallet $wallet;

    /**
     * @var string
     */

    protected string $guard_name = 'user';

    /**
     * @var array|string[]
     */

    protected $fillable = [
        'username',
        'national_code',
        'birthday',
        'father_name',
        'first_name',
        'last_name',
        'email',
        'email_is_verified',
        'mobile',
        'mobile_is_verified',
        'password',
        'avatar',
        'two_factor_is_enabled',
        'two_factor_type',
        'two_factor_secret',
        'two_factor_is_verified_until',
        'referrer_id',
        'ethereum_address'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
        'two_factor_is_enabled' => 'boolean',
        'email_is_verified' => 'boolean',
        'mobile_is_verified' => 'boolean',
        'two_factor_is_verified_until' => 'datetime',
    ];

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

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return MorphMany
     */

    public function answers(): MorphMany
    {
        return $this->morphMany(Answer::class, 'answerable');
    }

    /**
     * @return HasMany
     */

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    /**
     * @return BelongsTo
     */

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany
     */

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * @return HasMany
     */

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(bankAccount::class);
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

    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class);
    }

    /**
     * @return BelongsToMany
     */

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class)->withPivot(['document', 'status', 'reject_reason']
        )->withTimestamps();
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

    public function manualDeposits(): HasMany
    {
        return $this->hasMany(ManualDeposit::class);
    }

    /**
     * @return BelongsToMany
     */

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * @return HasOne
     */

    public function kyc(): HasOne
    {
        return $this->hasOne(Kyc::class);
    }

    /**
     * @return HasOne
     */

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * @return Wallet
     */

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    /**
     * @param  string|int  $symbol
     * @param  int  $type
     * @param  bool  $isVirtual
     * @return $this
     */

    public function setWallet(string|int $symbol, int $type, bool $isVirtual): static
    {
        /**
         * Fetch symbol.
         */

        $symbol = match (gettype($symbol)) {
            'integer' => Symbol::query()->findOrFail($symbol),
            default => Symbol::title($symbol)->firstOrFail()
        };

        /**
         * Return wallet.
         */

        $this->wallet = Wallet::firstOrCreate([
            'user_id' => $this->attributes['id'],
            'symbol_id' => $symbol->id,
            'type' => $type,
            'is_virtual' => $isVirtual
        ]);

        return $this;
    }

    /**
     * @param  float  $qty
     * @return void
     */

    public function chargeWallet(float $qty): void
    {
        $this->wallet->quantity += $qty;

        $this->wallet->save();

        unset($this->wallet);
    }

    /**
     * @param  Blockchain  $blockchain
     * @return mixed
     * @throws Exception
     */

    public function getWalletAddress(Blockchain $blockchain): mixed
    {
        DB::beginTransaction();
        try {
            /**
             * Check if user has wallet or not.
             */

            $walletAddress = self::walletAddresses()->isActive()->whereHas(
                'blockchain',
                fn($query) => $query->whereId($blockchain->id)
            );

            if ($walletAddress->exists()) {
                $walletAddress = $walletAddress->first();

                $walletAddress->last_used_at = now();

                $walletAddress->save();

                return $walletAddress;
            }

            /**
             * Seek into available wallet addresses.
             */

            $walletAddress = $blockchain->walletAddresses()->isActive()->available();

            if (!$walletAddress->exists()) {
                throw new NotFoundException(NotFoundException::WALLET_ADDRESS_NOT_FOUND);
            }

            $walletAddress = $walletAddress->first();

            $walletAddress->user()->associate($this->attributes['id'])->save();

            $walletAddress->allocated_at = now();

            $walletAddress->last_used_at = now();

            $walletAddress->save();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            throw $exception;
        }
        return $walletAddress->fresh();
    }

    /**
     * @return HasMany
     */

    public function walletAddresses(): HasMany
    {
        return $this->hasMany(WalletAddress::class);
    }
}
