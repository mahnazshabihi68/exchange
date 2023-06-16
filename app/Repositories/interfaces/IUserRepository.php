<?php

namespace App\Repositories\interfaces;

interface IUserRepository
{
    public function update($data, $userId);
}
