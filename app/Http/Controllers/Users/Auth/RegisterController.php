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
use App\Http\Requests\Auth\Register\StepOne;
use App\Http\Requests\Auth\Register\StepThree;
use App\Http\Requests\Auth\Register\StepTwo;
use App\Models\Otp;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware(['throttle:20,1']);
    }

    /**
     * @param StepOne $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function stepOne(StepOne $request): JsonResponse
    {
        try {

            /**
             * Validate and determine provided credential.
             */

//            $type = $this->determineCredentialType($request->credential);
              $type = 'email';

            /**
             * Let's keep going by check if user exists before or not.
             */

            $user = User::where($type, $request->credential);

            if ($user->exists()) {

                throw new Exception(__('messages.auth.register.alreadyExists'));

            }

            /**
             * User does not exist. So we can continue with send an OTP.
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

            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.auth.otp.successful')
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }

    }

    /**
     * @param StepThree $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function stepThree(StepThree $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Check otp is exists or not.
             */

            $otp = Otp::whereDestination($request->credential)->where('otp_is_verified', true);

            if (!$otp->exists()) {

                throw new Exception(__('messages.auth.register.invalidCredential'));

            }

            /**
             * Fetch credential type.
             */

//            $type = $this->determineCredentialType($request->credential);
              $type = 'email';

            /**
             * Prepare data.
             */

            $data = [
                $type => $request->credential,
                $type . '_is_verified' => true,
                'password' => bcrypt($request->password)
            ];

            /**
             * Check for referral.
             */

            if ($request->has('referrer-username')) {

                /**
                 * Trying to fetch referrer user.
                 */

                $referrerUser = User::where('username', $request->get('referrer-username'));

                if ($referrerUser->exists()) {

                    $data['referrer_id'] = $referrerUser->first()->id;

                }

            }

            /**
             * Create user.
             */

            $user = User::create($data);

            /**
             * Create access token.
             */

            $token = $user->createToken('access-token');
            DB::commit();

            /**
             * Return response.
             */

            return response()->json([
                'token' => $token->plainTextToken,
                'message' => __('messages.auth.register.successful')
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
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

