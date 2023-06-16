<?php

namespace App\Helpers;

use App\Enums\Log\LogChannelEnum;
use App\Enums\Log\LogLevelEnum;
use Illuminate\Support\Facades\Log;

class Logger
{
    /**
     * @var string
     */
    public const DEFAULT_LOG_MESSAGE = 'Default log message:';

    /**
     * @param  LogChannelEnum  $channel
     * @param  LogLevelEnum  $level
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function log(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogLevelEnum $level = LogLevelEnum::INFO,
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        if (!is_array($data)) {
            $data = [$data];
        }
        $data['loggable_type'] = $loggableType;
        $data['loggable_id'] = $loggableId;
        try {
            Log::channel($channel->value)->{$level->value}($message, $data);
        } catch (\Throwable $throwable) {
            $data['error_line'] = $throwable->getLine();
            $data['error_file'] = $throwable->getFile();
            $data['error_trace'] = $throwable->getTraceAsString();
            Log::critical($throwable->getMessage(), $data);
        }
    }

    /**
     * @param  LogChannelEnum  $channel
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function info(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        self::log(
            message: $message,
            data: $data,
            channel: $channel,
            loggableType: $loggableType,
            loggableId: $loggableId
        );
    }

    /**
     * @param  LogChannelEnum  $channel
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function error(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        self::log(
            message: $message,
            data: $data,
            level: LogLevelEnum::ERROR,
            channel: $channel,
            loggableType: $loggableType,
            loggableId: $loggableId
        );
    }

    /**
     * @param  LogChannelEnum  $channel
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function emergency(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        self::log(
            message: $message,
            data: $data,
            level: LogLevelEnum::EMERGENCY,
            channel: $channel,
            loggableType: $loggableType,
            loggableId: $loggableId
        );
    }

    /**
     * @param  LogChannelEnum  $channel
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function alert(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        self::log(
            message: $message,
            data: $data,
            level: LogLevelEnum::ALERT,
            channel: $channel,
            loggableType: $loggableType,
            loggableId: $loggableId
        );
    }

    /**
     * @param  LogChannelEnum  $channel
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function critical(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        self::log(
            message: $message,
            data: $data,
            level: LogLevelEnum::CRITICAL,
            channel: $channel,
            loggableType: $loggableType,
            loggableId: $loggableId
        );
    }

    /**
     * @param  LogChannelEnum  $channel
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function warning(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        self::log(
            message: $message,
            data: $data,
            level: LogLevelEnum::WARNING,
            channel: $channel,
            loggableType: $loggableType,
            loggableId: $loggableId
        );
    }

    /**
     * @param  LogChannelEnum  $channel
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function notice(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        self::log(
            message: $message,
            data: $data,
            level: LogLevelEnum::NOTICE,
            channel: $channel,
            loggableType: $loggableType,
            loggableId: $loggableId
        );
    }

    /**
     * @param  LogChannelEnum  $channel
     * @param  string  $message
     * @param  mixed  $data
     * @param  string|null  $loggableType
     * @param  int|null  $loggableId
     * @return void
     */
    public static function debug(
        string $message = self::DEFAULT_LOG_MESSAGE,
        mixed $data = [],
        LogChannelEnum $channel = LogChannelEnum::APPLICATION,
        ?string $loggableType = null,
        ?int $loggableId = null,
    ): void {
        self::log(
            message: $message,
            data: $data,
            level: LogLevelEnum::DEBUG,
            channel: $channel,
            loggableType: $loggableType,
            loggableId: $loggableId
        );
    }
}
