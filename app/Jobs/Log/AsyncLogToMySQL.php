<?php

namespace App\Jobs\Log;

use App\DTO\Log\LogEventDTO;
use App\Events\Log\MySQLLogIsNeeded;
use App\Services\interfaces\Log\ILogService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AsyncLogToMySQL implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param MySQLLogIsNeeded $event
     */
    public function __construct(private readonly MySQLLogIsNeeded $event)
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
        $logService->logToMySQL(new LogEventDTO((array)$this->event));
    }
}
