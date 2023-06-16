<?php

namespace App\Repositories\impls;

use App\Models\Kyc;
use App\Repositories\interfaces\IKycRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class KycRepository implements IKycRepository
{
    private Kyc $kyc;

    public function __construct(Kyc $kyc)
    {
        $this->kyc = $kyc;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->kyc::find($id);
    }

    /**
     * @param $data
     * @param $userId
     * @return mixed
     */
    public function create($data, $userId)
    {
        return $this->kyc::create([
            'user_id' => $userId,
            'authorization_kyc' => $data['clientToken'],
        ]);
    }

    /**
     * @param $kyc
     * @param $data
     * @return mixed
     */
    public function update($kyc, $data)
    {
        return $kyc->update([
            'status' => $data['status_code'],
            'details' => json_encode($data['message']),
        ]);
    }

    /**
     * @param $data
     * @param $userId
     * @return Kyc|null
     */
    public function getByAuthorizationKyc($data, $userId): ?Kyc
    {
        return $this->kyc::where('authorization_kyc', $data['clientToken'])->where('user_id', '!=', $userId)->first();
    }

    /**
     * @param $userId
     * @return Kyc|null
     */

    public function getByUserId($userId): ?Kyc
    {
        return $this->kyc::where('user_id', $userId)->first();
    }

    /**
     * @return Authenticatable
     */

    private function user(): Authenticatable
    {
        return auth('user')->user();
    }

    public function updateNewClientToken($data, $userId)
    {
        return $this->kyc::where('user_id', $userId)->update([
            'authorization_kyc' => $data['clientToken']
        ]);
    }
}
