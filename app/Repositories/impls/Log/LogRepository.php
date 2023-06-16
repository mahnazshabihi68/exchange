<?php

namespace App\Repositories\impls\Log;

use App\Models\Log;
use App\Repositories\impls\BaseRepository;
use App\Repositories\interfaces\Log\ILogRepository;

class LogRepository extends BaseRepository implements ILogRepository
{
    /**
     * @param  Log  $model
     */
    public function __construct(Log $model)
    {
        parent::__construct($model);
    }
}
