<?php

namespace App\Services\impls;

use App\Repositories\interfaces\ICallbackWithdrawFiatRepository;
use App\Services\interfaces\ICallbackWithdrawFiatService;
use App\Transformers\CallbackWithdrawFiatTransformer;

class CallbackWithdrawFiatService implements ICallbackWithdrawFiatService
{
    /**
     * @param ICallbackWithdrawFiatRepository $callbackWithdrawFiatRepository
     */
    public function __construct(private readonly ICallbackWithdrawFiatRepository $callbackWithdrawFiatRepository)
    {
    }

    public function create($data)
    {
        $callbackWithdrawFiatData = CallbackWithdrawFiatTransformer::toCallbackWithdrawFiatCreateEntity(
            $data
        );
        return $this->callbackWithdrawFiatRepository->create($callbackWithdrawFiatData);
    }
}
