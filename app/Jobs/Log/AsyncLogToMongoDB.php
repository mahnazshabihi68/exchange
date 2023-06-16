<?php

namespace App\Jobs\Log;

use App\DTO\Log\LogEventDTO;
use App\Events\Log\MongoDBLogIsNeeded;
use App\Services\interfaces\Log\ILogService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AsyncLogToMongoDB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param MongoDBLogIsNeeded $event
     */
    public function __construct(private readonly MongoDBLogIsNeeded $event)
    {
        $this->onConnection('redis')->onQueue('log')->delay(Carbon::now()->addSeconds(3));
    }

    /**
     * Execute the job.
     *
     * @param ILogService $logService
     * @return void
     * @throws UnknownProperties
     * @throws \JsonException
     */
    public function handle(ILogService $logService): void
    {
        $logService->logToMongoDB(new LogEventDTO((array)$this->event));
    }
}
