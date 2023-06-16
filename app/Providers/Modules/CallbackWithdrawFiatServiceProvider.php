<?php

namespace App\Providers\Modules;

use App\Repositories\impls\CallbackWithdrawFiatRepository;
use App\Repositories\interfaces\ICallbackWithdrawFiatRepository;
use App\Services\impls\CallbackWithdrawFiatService;
use App\Services\interfaces\ICallbackWithdrawFiatService;
use Illuminate\Support\ServiceProvider;

class CallbackWithdrawFiatServiceProvider extends ServiceProvider
{
    /**
     * All the container singletons that should be registered.
     *
     * @var array
     */
    public array $singletons = [
        ICallbackWithdrawFiatService::class => CallbackWithdrawFiatService::class,
        ICallbackWithdrawFiatRepository::class => CallbackWithdrawFiatRepository::class
    ];
}
