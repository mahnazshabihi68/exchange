<?php

namespace App\Services\interfaces;

use App\DTO\CallbackWithdrawFiatDTO;

interface ICallbackWithdrawFiatService
{
    public function create(CallbackWithdrawFiatDTO $data);
}
