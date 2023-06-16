<?php

namespace App\Jobs\Log;

use App\Events\Log\FileLogIsNeeded;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AsyncLogToFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param FileLogIsNeeded $event
     */
    public function __construct(private readonly FileLogIsNeeded $event)
    {
        $this->onConnection('redis')->onQueue('log')->delay(Carbon::now()->addSeconds(3));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::channel('file')->{$this->event->level}($this->event->message, $this->event->data);
    }
}
