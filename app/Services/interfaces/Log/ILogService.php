<?php

namespace App\Services\interfaces\Log;

use App\DTO\Log\LogEventDTO;
use JsonException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

interface ILogService
{
    /**
     * @param LogEventDTO $logEventDto
     * @return void
     * @throws UnknownProperties
     * @throws JsonException
     */
    public function logToMongoDB(LogEventDTO $logEventDto): void;

    /**
     * @param LogEventDTO $logEventDto
     * @return void
     * @throws UnknownProperties
     * @throws JsonException
     */
    public function logToMySQL(LogEventDTO $logEventDto): void;
}
