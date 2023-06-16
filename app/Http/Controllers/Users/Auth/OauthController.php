<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Auth;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Oauth\Oauth;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Laravel\Socialite\Facades\Socialite;

class OauthController extends Controller
{
    /**
     * @param Oauth $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function redirect(Oauth $request): JsonResponse
    {

        try {

            $socialiteUrl = Socialite::driver($request->provider)->stateless()->redirect()->getTargetUrl();

            /**
             * Return response.
             */

            return response()->json([
                'redirect_url' => $socialiteUrl
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage(),
            ], 400);

        }
    }

    /**
     * @param string $provider
     * @return RedirectResponse
     * @throws \JsonException
     */

    public function callback(string $provider): RedirectResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Check if provider is supported or not.
             */

            if (!config('services.' . $provider)) {

                abort(404);

            }

            /**
             * Authenticated user.
             */

            $authenticatedUser = Socialite::driver($provider)->stateless()->user();

            /**
             * Check if user already registered or not.
             */

            $userExists = User::whereEmail($authenticatedUser->getEmail());

            if ($userExists->exists()) {

                /**
                 * Generate access token.
                 */

                $token = $userExists->first()->createToken('access-token')->plainTextToken;

                /**
                 * Return redirect.
                 */

                return redirect()->away(config('app.frontend_url') . '/oauth/' . $token);

            }

            /**
             * User is not registered so we have to register.
             */

            $data = [
                'email' => $authenticatedUser->getEmail(),
                'email_is_verified' => true,
                'first_name' => null,
                'last_name' => null,
            ];

            /**
             * Check for users name.
             */

            if ($authenticatedUser->getName()) {

                if (str_contains($authenticatedUser->getName(), ' ')) {

                    $explodedName = explode(' ', $authenticatedUser->getName());

                    $data['first_name'] = $explodedName[0];

                    $data['last_name'] = $explodedName[1];

                } else {

                    $data['last_name'] = $authenticatedUser->getName();

                }

            }

            /**
             * Check for users avatar.
             */

//            if ($authenticatedUser->getAvatar()) {
//
//                $avatarPath = md5($authenticatedUser->getName());
//
//                Storage::exists('avatars') ? null : Storage::makeDirectory('avatars');
//
//                Image::make($authenticatedUser->getAvatar())->fit(128, 128)->save(public_path('storage/avatars/' . $avatarPath . '.jpg'));
//
//                $data['avatar'] = 'avatars/' . $avatarPath . '.jpg';
//
//            }

            /**
             * Create user and log him in.
             */

            $user = User::create($data);

            /**
             * Generate access token.
             */

            $token = $user->createToken('access-token')->plainTextToken;

            DB::commit();
            /**
             * Return redirect.
             */

            return redirect()->away(config('app.frontend_url') . '/oauth/' . $token);

        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return redirect()->away(config('app.frontend_url'));
        }
    }
}
