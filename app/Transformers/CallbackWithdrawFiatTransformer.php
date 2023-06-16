<?php

namespace App\Transformers;

use App\DTO\CallbackWithdrawFiatDTO;
use App\Models\CallbackWithdrawFiat;

class CallbackWithdrawFiatTransformer
{
    /**
     * @param CallbackWithdrawFiatDTO $callbackWithdrawFiatDTO
     * @return CallbackWithdrawFiat
     */
    public static function toCallbackWithdrawFiatCreateEntity(CallbackWithdrawFiatDTO $callbackWithdrawFiatDTO
    ): CallbackWithdrawFiat {
        $model = new CallbackWithdrawFiat();
        $model->setAttribute('withdraw_id', $callbackWithdrawFiatDTO->getWithdrawId())
            ->setAttribute('amount', $callbackWithdrawFiatDTO->getAmount())
            ->setAttribute('currency', $callbackWithdrawFiatDTO->getCurrency())
            ->setAttribute('description', $callbackWithdrawFiatDTO->getDescription())
            ->setAttribute('factor_number', $callbackWithdrawFiatDTO->getFactorNumber())
            ->setAttribute('destination_iban_number', $callbackWithdrawFiatDTO->getDestinationIbanNumber())
            ->setAttribute('owner_name', $callbackWithdrawFiatDTO->getOwnerName())
            ->setAttribute('reference_id', $callbackWithdrawFiatDTO->getReferenceId())
            ->setAttribute('source_iban_number', $callbackWithdrawFiatDTO->getSourceIbanNumber())
            ->setAttribute('transaction_status', $callbackWithdrawFiatDTO->getTransactionStatus())
            ->setAttribute('transfer_description', $callbackWithdrawFiatDTO->getTransferDescription())
            ->setAttribute('transfer_status', $callbackWithdrawFiatDTO->getTransferStatus())
            ->setAttribute('tracker_id', $callbackWithdrawFiatDTO->getTrackerId());
        return $model;
    }
}
