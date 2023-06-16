<?php

namespace App\Listeners\Log;

use App\DTO\Log\LogEventDTO;
use App\Events\Log\MySQLLogIsNeeded;
use App\Jobs\Log\AsyncLogToMySQL;
use App\Services\interfaces\Log\ILogService;
use JsonException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class LogToMySQL
{
    /**
     * Create the event listener.
     *
     * @param ILogService $logService
     */
    public function __construct(private readonly ILogService $logService)
    {
    }

    /**
     * @param MySQLLogIsNeeded $event
     * @return void
     * @throws JsonException
     * @throws UnknownProperties
     */
    public function handle(MySQLLogIsNeeded $event): void
    {
        if (config('app.async_log_to_mysql')) {
            AsyncLogToMySQL::dispatch($event);
            return;
        }
        $this->logService->logToMySQL(new LogEventDTO((array)$event));
    }
}
