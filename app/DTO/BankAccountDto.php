<?php

namespace App\DTO;

class BankAccountDto
{
    public static function toBankInfoDto($data)
    {
        return [
            'user_id'        => $data['user_id'],
            'sheba'          => $data['bankInfo']->shabaNumber,
            'card'           => $data['bankInfo']->cardNumber,
            'bank'           => $data['bankInfo']->bankName,
            'account_number' => $data['bankInfo']->accountNumber,
            'status'         => $data['status'],
            'from_kyc'       => $data['from_kyc']
        ];
    }
}
