<?php

namespace App\DTO;

class UserProfileDto
{
    public static function toUserProfileInfoDto($userProfile, $userId): array
    {
        return [
            'user_id'           =>  $userId,
            'first_name'        =>  $userProfile->name,
            'last_name'         =>  $userProfile->family,
            'national_code'     =>  $userProfile->nationalCode,
            'mobile'            =>  $userProfile->mobileNumber,
            'phone'             =>  $userProfile->phoneNumber,
            'birthday'          =>  $userProfile->birthDate,
            'state'             =>  $userProfile->addressInfo[0]->state,
            'city'              =>  $userProfile->addressInfo[0]->city,
            'address'           =>  $userProfile->addressInfo[0]->userAddress,
            'postal_code'       =>  $userProfile->addressInfo[0]->postalCode
        ];
    }
}
