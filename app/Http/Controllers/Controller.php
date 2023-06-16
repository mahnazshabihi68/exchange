<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers;

use App\Classes\SMSIR;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param int $length
     * @param string|null $table
     * @param string|null $column
     * @return int
     */

    public function tokenGenerator(int $length, string $table = null, string $column = null): int
    {
        $token = mt_rand(pow(10, $length - 1), pow(10, $length) - 1);

        if ($table && $column) {

            if (Schema::hasColumn($table, $column)) {

                $existence = DB::table($table)->where($column, $token)->exists();

                if ($existence) {

                    $this->tokenGenerator($length, $table, $column);

                }

            }

        }

        return $token;
    }

    /**
     * @return SMSIR
     */

    public function SMS(): SMSIR
    {
        return new SMSIR();
    }

    /**
     * @param string $credential
     * @return string
     * @throws Exception
     */

    public function determineCredentialType(string $credential): string
    {
        /**
         * Default type.
         */

        $type = 'mobile';

        $input = [
            'credential' => $credential
        ];

        $mobileValidation = Validator::make($input, [
            'credential' => 'ir_mobile'
        ]);

        if ($mobileValidation->fails()) {

            /**
             * Credential is not mobile so we check if it is email.
             */

            $emailValidation = Validator::make($input, [
                'credential' => 'email'
            ]);

            if ($emailValidation->fails()) {

                $type = 'username';

            } else {

                $type = 'email';

            }
        }

        return $type;

    }

}
