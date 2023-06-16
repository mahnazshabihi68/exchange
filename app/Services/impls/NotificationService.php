<?php

namespace App\Services\impls;

use App\Repositories\interfaces\INotificationRepository;
use App\Services\interfaces\INotificationService;

class NotificationService implements INotificationService
{
    private INotificationRepository $notificationRepository;

    public function __construct(INotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function create($data, $userId)
    {
        return $this->notificationRepository->create($data, $userId);
    }
}
