<?php

namespace App\Providers\Modules;




use App\Repositories\impls\Log\LogMongoDBRepository;
use App\Repositories\impls\Log\LogRepository;
use App\Repositories\interfaces\Log\ILogMongoDBRepository;
use App\Repositories\interfaces\Log\ILogRepository;
use App\Services\impls\Log\LogService;
use App\Services\interfaces\Log\ILogService;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * All the container singletons that should be registered.
     *
     * @var array
     */
    public array $singletons = [
        ILogService::class => LogService::class,
        ILogRepository::class => LogRepository::class,
        ILogMongoDBRepository::class => LogMongoDBRepository::class,
    ];
}
