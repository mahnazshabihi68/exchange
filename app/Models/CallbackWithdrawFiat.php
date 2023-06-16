<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallbackWithdrawFiat extends Model
{
    use HasFactory;

    /**
     * @var array|string[]
     */

    protected $fillable = [
        'withdraw_id',
        'amount',
        'currency',
        'description',
        'factor_number',
        'destination_iban_number',
        'owner_name',
        'reference_id',
        'source_iban_number',
        'transaction_status',
        'transfer_description',
        'transfer_status',
        'tracker_id',
    ];

    /**
     * @return BelongsTo
     */

    public function withdraw(): BelongsTo
    {
        return $this->belongsTo(Withdraw::class);
    }
}
