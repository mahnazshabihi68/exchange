<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins;

use App\Classes\SMSIR;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Jobs\SendSMS;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:sms']);

        $this->middleware(['throttle:3,1'])->only('sendTest');
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function getCredit(): JsonResponse
    {
        try {

            return response()->json([
                'sms-credit' => $this->SMSIR()->getCredit()
            ]);

        } catch (Exception|GuzzleException $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => __('messages.failed')
            ], 400);

        }
    }

    /**
     * @return SMSIR
     */

    private function SMSIR(): SMSIR
    {
        return new SMSIR();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function getLogs(Request $request): JsonResponse
    {
        try {

            $smsLogs = $this->SMSIR()->sentMessagesLogs(verta()->subMonths(3)->format('Y/m/d'), verta()->format('Y/m/d'), 1000, 1)->filter(function ($log) {
                return str_contains($log->SMSMessageBody, config('settings.name_fa'));
            });

            return response()->json([
                'sms-logs' => $smsLogs
            ]);

        } catch (Exception|GuzzleException $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => __('messages.failed')
            ], 400);

        }
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws \JsonException
     */
    public function sendTest(Request $request): JsonResponse
    {
        /**
         * Validate request.
         */

        $this->validate($request, [
            'destination' => 'required|string|ir_mobile'
        ]);

        try {

            /**
             * Send test SMS.
             */

            SendSMS::dispatch($request->destination, env('SMS_OTP'), [
                [
                    "Parameter" => "VerificationCode",
                    "ParameterValue" => 'TEST'
                ],
                [
                    "Parameter" => "PlatformName",
                    "ParameterValue" => config('settings.name_' . app()->getLocale())
                ],
            ]);

            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.successful'),
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => __('messages.failed')
            ]);

        }
    }
}
