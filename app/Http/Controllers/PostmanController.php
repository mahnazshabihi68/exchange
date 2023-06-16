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
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PostmanController extends Controller
{

    /**
     * @return BinaryFileResponse|JsonResponse
     * @throws \JsonException
     */

    public function download(): BinaryFileResponse|JsonResponse
    {
        try {

            /**
             * Fetch postman collection.
             */

            $collection = collect(Storage::disk('local')->files('postman'))->last();

            /**
             * Return download response.
             */

            return response()->download(storage_path('app/' . $collection));

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }
}
