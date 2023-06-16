<?php

namespace App\Http\Controllers\Users\Kyc;

use App\Http\Controllers\Controller;
use App\Services\interfaces\IKycService;
use Illuminate\Http\Request;

class KycController extends Controller
{
    protected IKycService $kycService;

    public function __construct(IKycService $kycService)
    {
        $this->kycService = $kycService;
    }

    public function getAuthorizationKycInfo(Request $request)
    {
        $data = $request->all();
        return $this->kycService->getAuthorizationKycInfo($data);
    }
}
