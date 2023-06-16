<?php

namespace App\Services\impls;

use App\DTO\ApiResponses;
use App\DTO\BankAccountDto;
use App\DTO\UserDto;
use App\DTO\UserProfileDto;
use App\Models\User;
use App\Repositories\interfaces\IKycRepository;
use App\Services\interfaces\IBankAccountService;
use App\Services\interfaces\IKycService;
use App\Services\interfaces\INotificationService;
use App\Services\interfaces\IUserProfileService;
use App\Services\interfaces\IUserService;
use App\Webservices\KycAuthorization\interfaces\IKycAuthorizationService;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Intervention\Image\Exception\NotFoundException;
use Spatie\Permission\Models\Permission;

class KycService implements IKycService
{
    private IKycRepository $kycRepository;
    private INotificationService $notificationService;
    private IKycAuthorizationService $kycAuthorizationService;
    private IBankAccountService $bankAccountService;
    private IUserProfileService $userProfileService;
    private IUserService $userService;

    public function __construct(
        IKycRepository           $kycRepository,
        INotificationService     $notificationService,
        IKycAuthorizationService $kycAuthorizationService,
        IBankAccountService      $bankAccountService,
        IUserProfileService      $userProfileService,
        IUserService             $userService
    )
    {
        $this->kycRepository = $kycRepository;
        $this->notificationService = $notificationService;
        $this->kycAuthorizationService = $kycAuthorizationService;
        $this->bankAccountService = $bankAccountService;
        $this->userProfileService = $userProfileService;
        $this->userService = $userService;
    }

    /**
     * @throws Exception
     */
    public function getAuthorizationKycInfo($data): ?\Illuminate\Http\JsonResponse
    {
        $userId = $data['user_id'] ?? $this->user()->id;

        if (isset($this->user($userId)->kyc) && $this->user($userId)->kyc->status == "success"){
            throw new Exception(Lang::get('messages.kyc.status-code.409'));
        }

        if (!isset($data['clientToken']) || (isset($data['kycCode']) && $data['kycCode'] != 1)) {
            $notification = $this->fillMessage(Lang::get('messages.kyc.status-code.' . $data['kycCode']));
            $this->notificationService->create($notification, $userId);

            return null;
        }

        //check exist client token
        if ($this->kycRepository->getByAuthorizationKyc($data, $userId)) {
            $notification = $this->fillMessage(Lang::get('messages.kyc.errors.exist-client-token'));
            $this->notificationService->create($notification, $userId);

            return null;
        }

        // get kyc by userId from repository
        $result = $this->kycRepository->getByUserId($userId);

        //check disagreement client token
        if ($result && $result['authorization_kyc'] != $data['clientToken']) {

            // this method is used when client went to UI and doesn't authorization,
            // so we need alternative new client token instead old client token in authorization_kys field
            $this->kycRepository->updateNewClientToken($data, $userId);

            $notification = $this->fillMessage(Lang::get('messages.kyc.errors.disagreement-client-token'));
            $this->notificationService->create($notification, $userId);

            return null;
        }

        // create kyc if kyc don't exist
        $kyc = $result ?? $this->kycRepository->create($data, $userId);

        // get userInfo from kyc authorization service
        $result = $this->kycAuthorizationService->getAuthorizationKycInfo($data);

        if ($result['status'] == 200) {

            $findKyc = $this->kycRepository->find($kyc->id);

            if (empty($findKyc)) {
                throw new NotFoundException(Lang::get('not fount' . $kyc->id));
            }

            $result['status_code'] = Lang::get('messages.kyc.status-code.' . $result['message']->status);

            $this->kycRepository->update($findKyc, $result);

            if ($result['message']->status == 'SUCCESS') {

                // create user profile
                $this->createUserProfile($result, $userId);

                // create bank account
                $this->createBankAccount($result, $userId);

                // update user
                $this->updateUser($result, $userId);

                /**
                 * Fetch permissions.
                 */
                $permissions = Permission::whereGuardName('user')->get();

                $this->user($userId)->givePermissionTo($permissions);
            }

            return ApiResponses::successResponse(null, $result['message']->message, $result['status']);

        } else return ApiResponses::errorResponse(null, $result['status']);
    }

    /**
     * @param $userId
     * @return Authenticatable
     */
    private function user($userId = null): Authenticatable
    {
        return Auth::check() ? auth('user')->user() : User::find($userId);
    }

    /**
     * @param $content
     * @return array
     */
    private function fillMessage($content): array
    {
        return [
            'title' => Lang::get('messages.kyc.status'),
            'content' => $content,
            'is_highlighted' => 1
        ];
    }

    /**
     * @param $data
     * @param $userId
     * @return mixed
     */
    public function createUserProfile($data, $userId): mixed
    {
        $userProfileKyc = UserProfileDto::toUserProfileInfoDto($data['message'], $userId);
        return $this->userProfileService->create($userProfileKyc);
    }

    /**
     * @param $data
     * @param $userId
     * @return mixed
     */
    public function createBankAccount($data, $userId)
    {
        $bankInfoData = [
            'bankInfo' => $data['message']->bankInfo,
            'user_id' => $userId,
            'status' => 1,
            'from_kyc' => 1
        ];
        $bankInfoKyc = BankAccountDto::toBankInfoDto($bankInfoData);
        return $this->bankAccountService->create($bankInfoKyc);
    }

    public function updateUser($data, $userId)
    {
        $userInfoKyc = UserDto::toUserDto($data['message']);
        $this->userService->update($userInfoKyc, $userId);
    }
}
