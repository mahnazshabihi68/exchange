<?php

namespace App\Services\interfaces;

interface INotificationService
{
    public function create($data, $userId);
}
