<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blockchain extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */

    protected $fillable = [
        'title',
        'name_fa',
        'name_en',
        'picture',
        'deposit_min_needed_confirmations',
        'explorer'
    ];

    /**
     * @var string[]
     */

    protected $casts = [
        'name_fa' => 'string',
        'name_en' => 'string',
        'title' => 'string',
        'deposit_min_needed_confirmations' => 'integer',
        'explorer' => 'string'
    ];

    /**
     * @return string
     */

    public function getPictureAttribute(): string
    {
        return config('filesystems.disks.public.url') . '/'. $this->attributes['picture'];
    }

    /**
     * @param $query
     * @param $title
     * @return mixed
     */

    public function scopeTitle($query, $title): mixed
    {
        return $query->whereTitle($title);
    }

    /**
     * @return HasMany
     */

    public function walletAddresses(): HasMany
    {
        return $this->hasMany(WalletAddress::class);
    }

    /**
     * @return BelongsToMany
     */

    public function symbols(): BelongsToMany
    {
        return $this->belongsToMany(Symbol::class)->withPivot('transfer_fee');
    }

    /**
     * @return mixed
     */

    public function getExplorer(): mixed
    {
        return new $this->attributes['explorer']();
    }
}
