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

class CheckAuthController extends Controller
{
    /**
     * @param string $guard
     * @return JsonResponse
     */

    public function check(string $guard): JsonResponse
    {
        if (in_array($guard, ['user', 'admin'])) {

            if (auth($guard)->check()) {

                $user = auth($guard)->user();

                return response()->json([
                    'guard' => $guard,
                    'user' => $user,
                    'permissions' => $user->getPermissionsViaRoles()
                ]);

            }

        }

        return response()->json([
            'error' => __('messages.auth.login.notAuthenticated')
        ], 401);
    }
}
