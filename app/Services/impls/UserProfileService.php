<?php

namespace App\Services\impls;

use App\Repositories\interfaces\IUserProfileRepository;
use App\Services\interfaces\IUserProfileService;

class UserProfileService implements IUserProfileService
{
    private IUserProfileRepository $userProfileRepository;

    public function __construct(IUserProfileRepository $userProfileRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
    }

    public function getByUserId($userId)
    {
        return $this->userProfileRepository->getByUserId($userId);
    }

    public function create($data)
    {
        return $this->userProfileRepository->create($data);
    }

    public function update($data, $userId)
    {
        return $this->userProfileRepository->update($data, $userId);
    }
}
