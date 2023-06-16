<?php

namespace App\Repositories\interfaces;

interface IKycRepository
{
    public function find($id);

    public function create($data, $userId);

    public function update($kyc, $data);

    public function updateNewClientToken($data, $userId);
}
