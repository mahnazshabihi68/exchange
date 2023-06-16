<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Exceptions;

use App\Exceptions\Primary\PrimaryBaseException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */

    public function register():void
    {
        $this->reportable(static function (PrimaryBaseException $e) {
            return false;
        });
        $this->reportable(function (Throwable $e) {

        });
    }

    /**
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|\Illuminate\Http\Response|Response
     * @throws Throwable
     */

    public function render($request, Throwable $e)
    {
        /**
         * Throttle response.
         */

        if ($e instanceof ThrottleRequestsException) {

            return response()->json([
                'error' => __('messages.throttle')
            ], 429);

        }

        /**
         * 403: Lack of permission response.
         */

        if ($e instanceof UnauthorizedException) {

            return response()->json([
                'error' => __('messages.permissions'),
            ], 403);

        }

        return parent::render($request, $e);

    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|Response
     */

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'error' => __('messages.auth.login.notAuthenticated')
        ], 401);
    }
}
