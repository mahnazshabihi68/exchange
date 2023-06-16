<?php

namespace App\Services\impls\Log\Drivers;

use App\Enums\Log\LogChannelEnum;

class LogProcessor
{
    /**
     * @param array $record
     * @return array
     */
    public function __invoke(array $record): array
    {
        $record['extra'] = [
            'user_id' => auth()->user() ? auth()->id() : null,
            'origin' => request()->headers->get('origin'),
            'ip' => request()?->server('REMOTE_ADDR'),
            'user_agent' => request()?->server('HTTP_USER_AGENT')
        ];

        return $record;
    }

    /**
     * @return array
     */
    public static function applicationLogDrivers(): array
    {
        $drivers = [];
        $lookup = [
            'log_to_file_enabled' => LogChannelEnum::FILE_ASYNC,
            'log_to_mysql_enabled' => LogChannelEnum::MYSQL,
            'log_to_mongo_db_enabled' => LogChannelEnum::MONGODB,
            'log_to_console_enabled' => LogChannelEnum::CONSOLE,
        ];
        foreach ($lookup as $key => $item) {
            if (config('app.' . $key)) {
                $drivers[] = $item->value;
            }
        }
        return $drivers;
    }
}
