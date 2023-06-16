<?php

namespace App\Transformers;

use App\DTO\Log\LogCreationDTO;
use App\DTO\Log\LogEventDTO;
use App\Helpers\Util;
use App\Models\Log;
use App\Models\LogMongoDB;
use JsonException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class LogTransformer
{
    /**
     * @throws UnknownProperties
     * @throws JsonException
     */
    public static function toLogCreationDTO(LogEventDTO $logEventDto): LogCreationDTO
    {
        $loggableType = $logEventDto->getData()['loggable_type'] ?? null;
        $loggableId = $logEventDto->getData()['loggable_id'] ?? null;
        return new LogCreationDTO(
            channel: $logEventDto->getChannel(),
            level: $logEventDto->getLevel(),
            data: Util::jsonEncodeUnicode($logEventDto->getData()),
            loggable_type: $loggableType,
            loggable_id: $loggableId,
            ip: $logEventDto->getIp(),
            device: $logEventDto->getDevice(),
            OS: $logEventDto->getOS(),
            browser: $logEventDto->getBrowser(),
        );
    }

    /**
     * @param  LogCreationDTO  $logCreationDTO
     * @return LogMongoDB
     */
    public static function toLogMongoCreateEntity(LogCreationDTO $logCreationDTO): LogMongoDB
    {
        $model = new LogMongoDB();
        $model->setAttribute('loggable_type', $logCreationDTO->getLoggableType())
            ?->setAttribute('loggable_id', $logCreationDTO->getLoggableId())
            ->setAttribute('channel', $logCreationDTO->getChannel())
            ->setAttribute('level', $logCreationDTO->getLevel())
            ->setAttribute('data', $logCreationDTO->getData())
            ->setAttribute('event', $logCreationDTO->getEvent())
            ->setAttribute('ip', $logCreationDTO->getIp())
            ->setAttribute('device', $logCreationDTO->getDevice())
            ->setAttribute('OS', $logCreationDTO->getOS())
            ->setAttribute('browser', $logCreationDTO->getBrowser());
        return $model;
    }

    /**
     * @param  LogCreationDTO  $logCreationDTO
     * @return Log
     */
    public static function toLogCreateEntity(LogCreationDTO $logCreationDTO): Log
    {
        $model = new Log();
        $model->setAttribute('loggable_type', $logCreationDTO->getLoggableType())
            ?->setAttribute('loggable_id', $logCreationDTO->getLoggableId())
            ->setAttribute('channel', $logCreationDTO->getChannel())
            ->setAttribute('level', $logCreationDTO->getLevel())
            ->setAttribute('data', $logCreationDTO->getData())
            ->setAttribute('event', $logCreationDTO->getEvent())
            ->setAttribute('ip', $logCreationDTO->getIp())
            ->setAttribute('device', $logCreationDTO->getDevice())
            ->setAttribute('OS', $logCreationDTO->getOS())
            ->setAttribute('browser', $logCreationDTO->getBrowser());
        return $model;
    }
}
