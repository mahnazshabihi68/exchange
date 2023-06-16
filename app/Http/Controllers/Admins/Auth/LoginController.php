<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins\Auth;

use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login\Login;
use App\Models\Admin;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * LoginController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:admin'])->only('logout');

        $this->middleware(['guest:admin'])->except('logout');
    }

    /**
     * @param Login $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function login(Login $request): JsonResponse
    {

        try {

            /**
             * Determine credential type.
             */

            // $credentialType = $this->determineCredentialType($request->credential);
            $credentialType = 'email';

            /**
             * Login attempt.
             */

            $admin = Admin::where($credentialType, $request->credential)->first();
            if(!$admin instanceof Admin){
                throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
            }

            if (!Hash::check($request->password, $admin->password)) {
                throw new Exception(__('messages.auth.login.failed'));

            }

            $token = $admin->createToken('access-token');

            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.auth.login.successful'),
                'token' => $token->plainTextToken,
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage(),
            ], 400);

        }

    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function logout(): JsonResponse
    {
        try {

            auth('admin')->user()->tokens()->delete();

            return response()->json([
                'message' => __('messages.auth.logout.successful')
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }
}
