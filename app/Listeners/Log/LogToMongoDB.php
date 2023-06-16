<?php

namespace App\Listeners\Log;

use App\DTO\Log\LogEventDTO;
use App\Events\Log\MongoDBLogIsNeeded;
use App\Jobs\Log\AsyncLogToMongoDB;
use App\Services\interfaces\Log\ILogService;
use JsonException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class LogToMongoDB
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
     * Handle the event.
     *
     * @param MongoDBLogIsNeeded $event
     * @return void
     * @throws JsonException
     * @throws UnknownProperties
     */
    public function handle(MongoDBLogIsNeeded $event): void
    {
        if (config('app.async_log_to_mongo_db')) {
            AsyncLogToMongoDB::dispatch($event);
            return;
        }
        $this->logService->logToMongoDB(new LogEventDTO((array)$event));
    }
}
