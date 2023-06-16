<?php

namespace App\Transformers;

use App\DTO\WithdrawFiatDTO;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Lang;

class WithdrawFiatTransformer
{
    /**
     * @param Withdraw $withdraw
     * @return WithdrawFiatDTO
     */
    public static function toWithdrawFiatDTO(Withdraw $withdraw
    ): WithdrawFiatDTO {
        return new WithdrawFiatDTO(
            amount: $withdraw->quantity - $withdraw->wage_quantity,
            description: Lang::get('messages.withdraw-fiat'),
            transferDescription: Lang::get('messages.withdraw-fiat'),
            ibanNumber: trim(str_before(str_after($withdraw->destination, ':'), ':')),
            ownerName: $withdraw->user->userProfile && $withdraw->user->userProfile->full_name ? $withdraw->user->userProfile->full_name : '',
            factorNumber: $withdraw->hash
        );
    }
}
