<?php

namespace App\Services\impls;

use App\Repositories\interfaces\IUserRepository;
use App\Services\interfaces\IUserService;

class UserService implements IUserService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        return $this->userRepository = $userRepository;
    }

    public function update($data, $userId): bool
    {
        return $this->userRepository->update($data, $userId);
    }
}
