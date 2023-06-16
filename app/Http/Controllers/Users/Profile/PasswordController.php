<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Profile;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Password\Update;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{

    /**
     * PasswordController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:user']);
    }

    /**
     * @param Update $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function update(Update $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Check current password validation.
             */

            $currentPasswordCheck = Hash::check($request->current_password, $this->user()->password);

            if (!$currentPasswordCheck) {

                throw new Exception(__('messages.profile.password.currentPasswordIsWrong'));

            }

            /**
             * Check whether current and new passwords are same or not.
             */

            $samePasswordsCheck = strcmp($request->password, $request->current_password);

            if ($samePasswordsCheck === 0) {

                throw new Exception(__('messages.profile.password.samePasswordError'));

            }

            /**
             * Everything seems to be ok.
             */

            /**
             * Change password.
             */

            $this->user()->update([
                'password' => bcrypt($request->password),
            ]);

            /**
             * Logout of account.
             */

            $this->user()->currentAccessToken()->delete();
            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.profile.password.successful')
            ]);

        } catch (Exception $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }

    }

    /**
     * @return Authenticatable
     */

    private function user(): Authenticatable
    {
        return auth('user')->user();
    }

}
