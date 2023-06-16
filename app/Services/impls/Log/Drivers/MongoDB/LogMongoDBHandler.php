<?php

namespace App\Services\impls\Log\Drivers\MongoDB;

use App\Events\Log\MongoDBLogIsNeeded;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class LogMongoDBHandler extends AbstractProcessingHandler
{
    /**
     * @param int $level
     */
    public function __construct(int $level = Logger::DEBUG)
    {
        parent::__construct($level);
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        $data = $record['context'];
        if (array_key_exists('extra', $record)) {
            $data = array_merge($record['context'], $record['extra']);
        }
        event(
            new MongoDBLogIsNeeded(
                channel: $record['channel'],
                level: $record['level_name'],
                message: $record['message'],
                data: $data
            )
        );
    }
}
