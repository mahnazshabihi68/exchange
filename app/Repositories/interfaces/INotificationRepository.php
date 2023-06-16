<?php

namespace App\Repositories\interfaces;

interface INotificationRepository
{
    public function create($data, $userId);
}
