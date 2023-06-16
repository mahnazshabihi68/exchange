<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kyc extends Model
{
    use HasFactory;

     /**
     * @var array|string[]
     */

    protected $fillable = [
        'user_id',
        'authorization_kyc',
        'details',
        'status'
    ];

    /**
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
    	return $this->belongsTo(User::class);
    }

}
