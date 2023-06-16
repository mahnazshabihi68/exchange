<?php

namespace App\Repositories\impls;

use App\Models\UserProfile;
use App\Repositories\interfaces\IUserProfileRepository;

class UserProfileRepository implements IUserProfileRepository
{
    private UserProfile $userProfile;

    public function __construct(UserProfile $userProfile)
    {
        return $this->userProfile = $userProfile;
    }

    public function getByUserId($userId)
    {
        return $this->userProfile->where('user_id', $userId)->first();
    }

    public function create($data)
    {
        return $this->userProfile->create($data);
    }

    public function update($data, $userId): bool
    {
        return $this->userProfile->where('user_id', $userId)->update($data);
    }
}
