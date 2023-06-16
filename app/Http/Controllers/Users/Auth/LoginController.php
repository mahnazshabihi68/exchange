<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Auth;

use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login\Login;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class LoginController extends Controller
{
    /**
     * LoginController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:user'])->only('logout');

        $this->middleware(['guest:user'])->only('login');

        $this->middleware(['throttle:20,1']);
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

//            $credentialType = $this->determineCredentialType($request->credential);
            $credentialType = 'email';

            /**
             * Login attempt.
             */

            $user = User::where($credentialType, $request->credential)->first();
            if(!$user instanceof User){
                throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
            }

            if (!Hash::check($request->password, $user->password)) {

                throw new Exception(__('messages.auth.login.failed'));

            }

            /**
             * Generate token.
             */

            $token = $user->createToken('access-token');

            /**
             * Log.
             */

            $this->loginLogger($user);

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

            $user = User::find(auth('user')->user()->id);

            $user->tokens()->delete();

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

    private function loginLogger(User $user)
    {
        $browser = $this->agent()->browser();

        $browserVersion = $this->agent()->version($browser);

        $OS = $this->agent()->platform();

        $OSVersion = $this->agent()->version($OS);

        $user->logs()->create([
            'event' => 1,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'device' => $this->agent()->deviceType(),
            'browser' => $browser . ' ' . $browserVersion,
            'OS' => $OS . ' ' . $OSVersion
        ]);
    }

    /**
     * @return Agent
     */

    private function agent(): Agent
    {
        return new Agent();
    }
}
