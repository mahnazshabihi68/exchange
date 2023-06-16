<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins\Accountancy;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Models\Accountancy;
use Exception;
use Illuminate\Http\JsonResponse;

class AccountancyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:accountancy']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {
            /**
             * Return response.
             */

            return response()->json([
                'accountancies' => Accountancy::latest()->take(50)->get()
            ]);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Accountancy $accountancy
     * @return JsonResponse
     * @throws \JsonException
     */

    public function show(Accountancy $accountancy): JsonResponse
    {
        try {

            return response()->json([
                'accountancy'   =>  $accountancy
            ]);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function store(): JsonResponse
    {
        try {

            /**
             * Create new accountancy.
             */

            $accountancy = (new Accountancy())->createCumulativeAccountancy();

            /**
             * Return response.
             */

            return response()->json([
                'message'   =>  __('messages.accountancy.store.successful'),
                'accountancy'   =>  $accountancy
            ], 201);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }
}
