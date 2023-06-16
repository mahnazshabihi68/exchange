<?php

namespace App\DTO;

class CryptoWithdrawDto
{
    /**
     * @param $data
     * @return array
     */
    public static function toCryptoWithdrawDto($data): array
    {
        $destinationAddress = trim(str_after($data['destination'], ':'));
        $value = $data['quantity'] - $data['wage_quantity'];
        $quantity = (is_numeric($value) && floor($value) != $value) ? $value : trim(str_before($value, '.'));

        return [
            'from_wallet' => $data['public_key'],
            'to_wallet' => $destinationAddress,
            //todo quantity must integer
            'value' => round($quantity, 0),
            'private_key' => $data['private_key'],
            'symbol' => strtolower($data['symbol']),
            'blockchain' => strtolower($data['blockchain']),
            'fee' => $data['fee'],
            'node' => strtolower($data['node'])
        ];
    }
}
