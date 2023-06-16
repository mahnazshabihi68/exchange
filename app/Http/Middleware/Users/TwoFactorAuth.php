<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Middleware\Users;

use Closure;
use Illuminate\Http\Request;

class TwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        /**
         * Check if user is logged in or not.
         */

        if (auth('user')->check()) {

            /**
             * User is logged in, So we have to declare user.
             */

            $user = auth('user')->user();

            /**
             * Check if user enabled 2fa before or not.
             */

            if ($user->two_factor_is_enabled) {

                /**
                 * Check if route pattern is in 2fa or not.
                 */

                if (!$request->routeIs('user.2fa.*')) {

                    /**
                     * Check status of 2fa.
                     */

                    if (is_null($user->two_factor_is_verified_until) || now()->diffInMinutes($user->two_factor_is_verified_until) <= 0) {

                        /**
                         * User has to submit 2fa.
                         */

                        return response()->json([
                            'type' => '2fa',
                            'error' => __('messages.auth.2fa.enter2fa'),
                        ], 403);

                    }

                }

            }

        }

        return $next($request);
    }
}
