<?php

namespace App\Repositories\impls;

use App\Models\User;
use App\Repositories\interfaces\INotificationRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class NotificationRepository implements INotificationRepository
{
    public function create($data, $userId)
    {
        return $this->user($userId)->notifications()->create($data);
    }

    /**
     * @return Authenticatable
     */

    private function user($userId): Authenticatable
    {
        return Auth::check() ? auth('user')->user() : User::find($userId);
    }
}
