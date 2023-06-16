<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class LogController extends Controller
{
    /**
     * LogController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:user']);
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'logs' => $this->user()->logs()->latest()->get(),
        ]);
    }

    /**
     * @return Authenticatable|null
     */

    private function user(): Authenticatable
    {
        return auth('user')->user();
    }
}
