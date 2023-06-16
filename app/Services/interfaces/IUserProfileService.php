<?php

namespace App\Services\interfaces;

interface IUserProfileService
{
    public function getByUserId($userId);

    public function create($data);

    public function update($data, $userId);
}
