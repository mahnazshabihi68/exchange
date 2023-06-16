<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Auth;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OTP\Send;
use App\Http\Requests\Auth\ResetPassword\StepOne;
use App\Http\Requests\Auth\ResetPassword\StepTwo;
use App\Jobs\SendEmail;
use App\Jobs\SendSMS;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['throttle:20,1']);
    }


    /**
     * @param StepOne $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function stepOne(StepOne $request): JsonResponse
    {
        try {

            /**
             * Determine credential type.
             */

//            $type = $this->determineCredentialType($request->credential);
              $type = 'email';

            /**
             * Fetch user.
             */

            $user = User::where($type, $request->credential)->where($type . '_is_verified', true);

            if (!$user->exists()) {

                throw new Exception(__('messages.auth.register.invalidCredential'));

            }

            /**
             * Send OTP.
             */

            $this->OTP()->send($type, $request->credential);

            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.auth.otp.enterToken', ['destination' => $request->credential])
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage(),
            ], 400);

        }

    }

    /**
     * @return OTPController
     */

    private function OTP(): OTPController
    {
        return new OTPController();
    }

    /**
     * @param StepTwo $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function stepTwo(StepTwo $request): JsonResponse
    {
        try {

            /**
             * Validate token.
             */

            $this->OTP()->verify($request->token, $request->credential);

//            $type = $this->determineCredentialType($request->credential);
              $type = 'email';

            $newPassword = Str::random(5).'@'.rand(10000,99999);

            $user = User::where($type, $request->credential)->firstOrFail();

            $user->update([
                'password' => bcrypt($newPassword),
            ]);

//            match ($type) {
//                'mobile' => SendSMS::dispatch($request->credential, env('SMS_NEWPASSWORD'), [
//                    [
//                        "Parameter" => "NewPassword",
//                        "ParameterValue" => $newPassword
//                    ],
//                    [
//                        "Parameter" => "PlatformName",
//                        "ParameterValue" => config('settings.name_' . app()->getLocale())
//                    ],
//                ]),
//                'email' => SendEmail::dispatch($request->credential, __('messages.auth.forgot.newPassword.subject'), __('messages.auth.forgot.newPassword.body', ['password' => $newPassword]))
//            };

            SendEmail::dispatch($request->credential, __('messages.auth.forgot.newPassword.subject'), __('messages.auth.forgot.newPassword.body', ['password' => $newPassword]));


            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.auth.forgot.successful', ['destination' => $request->credential]),
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Send $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function resendOTP(Send $request): JsonResponse
    {
        try {

            $type = $this->determineCredentialType($request->credential);

            $this->OTP()->send($type, $request->credential);

            return response()->json([
                'message' => __('messages.auth.otp.resend.successful')
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage(),
            ], 400);
        }
    }
}
