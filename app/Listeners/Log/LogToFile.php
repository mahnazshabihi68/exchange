<?php

namespace App\Listeners\Log;

use App\Events\Log\FileLogIsNeeded;
use App\Jobs\Log\AsyncLogToFile;
use Illuminate\Support\Facades\Log;

class LogToFile
{
    /**
     * Handle the event.
     *
     * @param FileLogIsNeeded $event
     * @return void
     */
    public function handle(FileLogIsNeeded $event): void
    {
        if (config('app.async_log_to_file')) {
            AsyncLogToFile::dispatch($event);
            return;
        }
        Log::channel('file')->{$event->level}($event->message, $event->data);
    }
}
