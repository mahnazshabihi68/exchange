<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }
}
