<?php

namespace App\Services\interfaces;

interface IKycService
{
    public function getAuthorizationKycInfo($data);
}
