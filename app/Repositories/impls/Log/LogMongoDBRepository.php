<?php

namespace App\Repositories\impls\Log;

use App\Models\LogMongoDB;
use App\Repositories\impls\BaseRepository;
use App\Repositories\interfaces\Log\ILogMongoDBRepository;

class LogMongoDBRepository extends BaseRepository implements ILogMongoDBRepository
{
    /**
     * @param  LogMongoDB  $model
     */
    public function __construct(LogMongoDB $model)
    {
        parent::__construct($model);
    }
}
