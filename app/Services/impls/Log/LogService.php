<?php

namespace App\Services\impls\Log;

use App\DTO\Log\LogEventDTO;
use App\Repositories\interfaces\Log\ILogMongoDBRepository;
use App\Repositories\interfaces\Log\ILogRepository;
use App\Services\interfaces\Log\ILogService;
use App\Transformers\LogTransformer;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogService implements ILogService
{
    /**
     * @param  ILogRepository  $logRepository
     * @param  ILogMongoDBRepository  $logMongoDBRepository
     */
    public function __construct(
        private readonly ILogRepository $logRepository,
        private readonly ILogMongoDBRepository $logMongoDBRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function logToMongoDB(LogEventDTO $logEventDto): void
    {
        try {
            $logCreationDTO = LogTransformer::toLogCreationDTO($logEventDto);
            $logMongoDBModel = LogTransformer::toLogMongoCreateEntity($logCreationDTO);
            $this->logMongoDBRepository->create($logMongoDBModel);
        } catch (Throwable $throwable) {
            $data = ['line' => $throwable->getLine(), 'file' => $throwable->getFile()];
            Log::critical($throwable->getMessage(), $data);
        }
    }

    /**
     * @inheritDoc
     */
    public function logToMySQL(LogEventDTO $logEventDto): void
    {
        try {
            $logCreationDTO = LogTransformer::toLogCreationDto($logEventDto);
            $logModel = LogTransformer::toLogCreateEntity($logCreationDTO);
            $this->logRepository->create($logModel);
        } catch (Throwable $throwable) {
            $data = ['line' => $throwable->getLine(), 'file' => $throwable->getFile()];
            Log::critical($throwable->getMessage(), $data);
        }
    }
}
