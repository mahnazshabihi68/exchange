<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Models\PrivacyPolicy;
use Exception;
use Illuminate\Http\JsonResponse;

class PrivacyPolicyController extends Controller
{
    /**
     * @return JsonResponse
     * @throws \JsonException
     */


    public function list(): JsonResponse
    {
        try {

            return response()->json([
                'privacy-policies' => PrivacyPolicy::latest()->get()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param PrivacyPolicy $privacyPolicy
     * @return JsonResponse
     * @throws \JsonException
     */

    public function show(PrivacyPolicy $privacyPolicy): JsonResponse
    {
        try {

            return response()->json([
                'privacy-policy' => $privacyPolicy
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }
}
