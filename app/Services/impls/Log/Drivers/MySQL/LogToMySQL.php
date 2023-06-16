<?php

namespace App\Services\impls\Log\Drivers\MySQL;

use App\Enums\Log\LogChannelEnum;
use App\Services\impls\Log\Drivers\LogProcessor;
use Monolog\Logger;

class LogToMySQL
{
    /**
     * Create a custom Monolog instance.
     *
     * @param array $config
     * @return Logger
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger(LogChannelEnum::MYSQL->value);
        $logger->pushHandler(new LogMySQLHandler());
        $logger->pushProcessor(new LogProcessor());

        return $logger;
    }
}
