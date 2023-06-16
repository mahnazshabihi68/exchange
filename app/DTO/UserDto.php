<?php

namespace App\DTO;

class UserDto
{
    /**
     * @param $data
     * @return array
     */
    public static function toUserDto($data): array
    {
        return [
            'first_name'          =>  $data->name,
            'last_name'           =>  $data->family,
            'national_code'       =>  $data->nationalCode,
            'mobile'              =>  $data->mobileNumber,
            'birthday'            =>  $data->birthDate,
            'mobile_is_verified'  => 1
        ];
    }
}
