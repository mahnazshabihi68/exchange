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
use App\Models\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
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
            'notifications' => $this->user()->notifications()->latest()->get()
        ]);
    }

    /**
     * @return Authenticatable
     */

    public function user(): Authenticatable
    {
        return auth('user')->user();
    }

    /**
     * @param Notification $notification
     * @return JsonResponse
     */

    public function show(Notification $notification): JsonResponse
    {
        $notification->update([
            'is_seen' => true
        ]);

        return response()->json([
            'notification' => $notification
        ]);
    }
}
