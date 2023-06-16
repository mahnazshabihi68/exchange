<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Users;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\Auth\OTPController;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Generator;

class TwoFactorAuthController extends Controller
{
    /**
     * TwoFactorAuthController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:user']);

        $this->middleware(['throttle:20,1']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     * @throws \JsonException
     */

    public function enable(Request $request): JsonResponse
    {
        /**
         * Check if already enabled or not.
         */

        if ($this->user()->two_factor_is_enabled) {

            return response()->json([
                'error' => __('messages.auth.2fa.alreadyEnabled')
            ], 400);

        }

        /**
         * Validate request.
         */

        $this->validate($request, [
            'two_factor_type' => 'required|in:mobile,email,google'
        ]);

        if (in_array($request->two_factor_type, ['mobile', 'email'])) {

            /**
             * Check if requested 2FA is entered and verified or what.
             */

            if (!$this->user()->{$request->two_factor_type} || !$this->user()->{$request->two_factor_type . '_is_verified'}) {

                return response()->json([
                    'error' => __('messages.auth.2fa.notEnoughData'),
                ], 400);

            }

            /**
             * Send an OTP.
             */

            try {

                $this->OTP()->send($request->two_factor_type, $this->user()->{$request->two_factor_type});

                $this->user()->update([
                    'two_factor_type' => $request->two_factor_type
                ]);

                return response()->json([
                    'message' => __('messages.auth.otp.enterToken', ['destination' => $this->user()->{$request->two_factor_type}]),
                ]);

            } catch (Exception $exception) {
                Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
                return response()->json([
                    'error' => $exception->getMessage()
                ], 400);

            }

        } elseif ($request->two_factor_type === 'google') {

            $secretKey = $this->google()->generateSecretKey();

            $this->user()->update([
                'two_factor_type' => $request->two_factor_type,
                'two_factor_secret' => encrypt($secretKey),
            ]);

            return response()->json([
                'secret' => $secretKey,
                'QrCode' => $this->QrCode()->size(200)->margin(3)->format('svg')->generate($this->google()->getQRCodeUrl(config('settings.name_en'), $this->user()->email ?? $this->user()->mobile, $secretKey))->toHtml()
            ]);

        }

    }

    /**
     * @return Authenticatable|null
     */

    private function user(): Authenticatable
    {
        return auth('user')->user();
    }

    /**
     * @return OTPController
     */

    private function OTP(): OTPController
    {
        return new OTPController();
    }

    /**
     * @return Google2FA
     */

    private function google(): Google2FA
    {
        return new Google2FA();
    }

    /**
     * @return Generator
     */

    private function QrCode(): Generator
    {
        return new Generator();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function disable(Request $request): JsonResponse
    {
        /**
         * Check if it's already disabled or what.
         */

        if (!$this->user()->two_factor_is_enabled) {

            return response()->json([
                'error' => __('messages.auth.2fa.alreadyDisabled')
            ], 400);

        }

        $this->user()->update([
            'two_factor_is_enabled' => false,
            'two_factor_is_verified' => false,
            'two_factor_expires_at' => null,
            'two_factor_type' => null,
            'two_factor_secret' => null
        ]);

        return response()->json([
            'message' => __('messages.auth.2fa.disable.successful')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */

    public function verify(Request $request): JsonResponse
    {
        /**
         * This endpoint may call to complete enabling 2FA process OR Verify the currently enabled 2FA.
         */

        /**
         * Check if user has @two_factor_type or not.
         */

        if (!$this->user()->two_factor_type) {

            return response()->json([
                'error' => __('messages.auth.2fa.notEnoughData')
            ], 400);

        }

        /**
         * Validate request.
         */

        $this->validate($request, [
            'otp' => 'required|numeric'
        ]);

        /**
         * Verify Token.
         */

        if (in_array($this->user()->two_factor_type, ['email', 'mobile'])) {

            try {

                $this->OTP()->verify($request->otp, $this->user()->{$this->user()->two_factor_type});

            } catch (Exception $exception) {

                return response()->json([
                    'error' => $exception->getMessage(),
                ], 400);
            }

        } elseif ($this->user()->two_factor_type === 'google') {

            $googleVerify = $this->google()->verify($request->otp, decrypt($this->user()->two_factor_secret));

            if (!$googleVerify) {

                return response()->json([
                    'error' => __('messages.auth.otp.wrongToken')
                ], 400);

            }

        }

        /**
         * Update user.
         */

        $this->user()->update([
            'two_factor_is_verified_until' => now()->addHour()
        ]);

        if (!$this->user()->two_factor_is_enabled) {

            $this->user()->update([
                'two_factor_is_enabled' => true
            ]);

        }

        return response()->json([
            'message' => __('messages.auth.otp.successful')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function sendOTP(Request $request): JsonResponse
    {
        if (!$this->user()->two_factor_type || !in_array($this->user()->two_factor_type, ['email', 'mobile'])) {

            return response()->json([
                'error' => __('messages.auth.2fa.notEnoughData'),
            ], 400);

        }

        try {

            $this->OTP()->send($this->user()->two_factor_type, $this->user()->{$this->user()->two_factor_type});

            return response()->json([
                'message' => __('messages.auth.otp.enterToken', ['destination' => $this->user()->{$this->user()->two_factor_type}]),
            ]);

        } catch (Exception $exception) {

            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }
}
