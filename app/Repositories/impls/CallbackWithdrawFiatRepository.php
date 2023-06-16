<?php

namespace App\Repositories\impls;

use App\Models\CallbackWithdrawFiat;
use App\Repositories\interfaces\ICallbackWithdrawFiatRepository;

class CallbackWithdrawFiatRepository extends BaseRepository implements ICallbackWithdrawFiatRepository
{
    /**
     * @param CallbackWithdrawFiat $model
     */
    public function __construct(CallbackWithdrawFiat $model)
    {
        parent::__construct($model);
    }
}
