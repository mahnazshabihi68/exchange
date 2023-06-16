<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class AppStatusController extends Controller
{

    /**
     * @return JsonResponse
     */

    public function status(): JsonResponse
    {
        return response()->json([
            'maintenance-mode' => File::exists(storage_path('/framework/down')),
        ]);
    }
}
