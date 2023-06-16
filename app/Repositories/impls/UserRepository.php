<?php

namespace App\Repositories\impls;

use App\Models\User;
use App\Repositories\interfaces\IUserRepository;

class UserRepository implements IUserRepository
{
    private User $user;

    public function __construct(User $user)
    {
        return $this->user = $user;
    }

    public function update($data, $userId): bool
    {
        return $this->user->where('id', $userId)->update($data);
    }
}
