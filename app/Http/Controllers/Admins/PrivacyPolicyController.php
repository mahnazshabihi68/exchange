<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\PrivacyPolicy\Store;
use App\Http\Requests\Admins\PrivacyPolicy\Update;
use App\Models\PrivacyPolicy;
use Exception;
use Illuminate\Http\JsonResponse;

class PrivacyPolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:privacy-policies']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {

            return response()->json([
                'privacy-policies' => PrivacyPolicy::latest()->get(),
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

    /**
     * @param Store $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function store(Store $request): JsonResponse
    {
        try {

            $privacyPolicy = PrivacyPolicy::create($request->all());

            return response()->json([
                'privacy-policy' => $privacyPolicy,
                'message' => __('messages.privacy-policies.store.successful')
            ], 201);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Update $request
     * @param PrivacyPolicy $privacyPolicy
     * @return JsonResponse
     * @throws \JsonException
     */

    public function update(Update $request, PrivacyPolicy $privacyPolicy): JsonResponse
    {
        try {

            $privacyPolicy->update($request->all());

            return response()->json([
                'privacy-policy' => $privacyPolicy->fresh(),
                'message' => __('messages.privacy-policies.update.successful')
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

    public function destroy(PrivacyPolicy $privacyPolicy): JsonResponse
    {
        try {

            $privacyPolicy->delete();

            return response()->json([
                'message' => __('messages.privacy-policies.destroy.successful')
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }
}
