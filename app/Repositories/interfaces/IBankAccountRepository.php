<?php

namespace App\Repositories\interfaces;

interface IBankAccountRepository
{
    public function getByUserId($userId);

    public function create($data);

    public function update($data, $userId);
}
