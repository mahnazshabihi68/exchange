<?php

namespace App\Webservices\KycAuthorization\interfaces;

interface IKycAuthorizationService
{
    public function getAccessToken();

    public function getAuthorizationKycInfo($data);

    public function getClientTokenByNationalCode($nationalCode);
}
