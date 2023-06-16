<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Jobs\SendSMS;
use App\Models\Otp;
use Exception;

class OTPController extends Controller
{
    /**
     * @var int
     */

    protected int $delayBetweenEachOTP;

    /**
     * @var int
     */

    protected int $otpValidationTime;

    /**
     * @var int
     */

    protected int $tokenLength;

    /**
     * OTPController constructor.
     */

    public function __construct()
    {
        $this->delayBetweenEachOTP = 30;

        $this->otpValidationTime = 30;

        $this->tokenLength = 6;
    }

    /**
     * @param string $type
     * @param string $destination
     * @return bool
     * @throws Exception
     */

    public function send(string $type, string $destination): bool
    {
        /**
         * Let's start by checking last time we send a token.
         */

        $otpExist = Otp::whereDestination($destination)->where('otp_is_verified', false);

        if ($otpExist->exists()) {

            $lastSentTokenDiffInSeconds = now()->diffInSeconds($otpExist->latest()->first()->otp_sent_at);

            if ($lastSentTokenDiffInSeconds < $this->delayBetweenEachOTP) {

                throw new Exception(__('messages.auth.otp.wait', ['seconds' => $this->delayBetweenEachOTP - $lastSentTokenDiffInSeconds]), 400);

            }

        }

        /**
         * Generate token and store it in database.
         */

        $otp = $this->tokenGenerator($this->tokenLength);

        Otp::create([
            'destination' => $destination,
            'destination_type' => $type,
            'otp' => $otp,
            'otp_sent_at' => now()
        ]);

        if ($type === 'mobile') {

            $data = [
                [
                    "Parameter" => "VerificationCode",
                    "ParameterValue" => $otp
                ],
                [
                    "Parameter" => "PlatformName",
                    "ParameterValue" => config('settings.name_' . app()->getLocale())
                ],
            ];

            SendSMS::dispatch($destination, env('SMS_OTP'), $data);

        } elseif ($type === 'email') {

            SendEmail::dispatch($destination, __('messages.auth.otp.EmailVerification'), __('messages.auth.otp.YourVerificationCode', ['token' => $otp]));

        }

        return true;
    }

    /**
     * @param int $token
     * @param string $destination
     * @return bool
     * @throws Exception
     */

    public function verify(int $token, string $destination): bool
    {
        $otp = Otp::whereDestination($destination)->where('otp_is_verified', false)->latest()->first();

        if (!$otp || $token != $otp->otp || now()->addSeconds($this->otpValidationTime) < $otp->otp_sent_at) {

            throw new Exception(__('messages.auth.otp.wrongToken'), 400);

        }

        $otp->update([
            'otp_is_verified' => true
        ]);

        return true;

    }
}
