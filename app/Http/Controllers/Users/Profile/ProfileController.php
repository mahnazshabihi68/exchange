<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Profile;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\Auth\OTPController;
use App\Http\Requests\Users\Profile\Update;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use function __;
use function auth;
use function public_path;
use function response;

class ProfileController extends Controller
{
    /**
     * ProfileController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:user']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {
            $user = User::with(['userProfile', 'kyc', 'bankAccounts', 'logs', 'notifications'])->find($this->user()->id);

            return response()->json($user);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @return Authenticatable
     */

    private function user(): Authenticatable
    {
        return auth('user')->user();
    }

    /**
     * @param Update $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function update(Update $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $data = $request->all();

            if ($this->user()->mobile !== $request->mobile) {

                $data['mobile_is_verified'] = false;

            }

            if ($this->user()->email !== $request->email) {

                $data['email_is_verified'] = false;

            }

            if ($request->hasFile('avatar')) {

                /**
                 * Upload new avatar and fit it 128 * 128 px.
                 */

                $data['avatar'] = $request->file('avatar')->store('avatars');

                Image::make(public_path('storage/' . $data['avatar']))->fit(128, 128)->save();

            }

            $this->user()->update($data);
            DB::commit();
            return response()->json([
                'message' => __('messages.profile.update.successful'),
            ]);

        } catch (Exception $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @throws ValidationException
     * @throws \JsonException
     */
    public function sendOTP(Request $request): JsonResponse
    {
        /**
         * Validate request.
         */

        $this->validate($request, [
            'type' => 'required|in:mobile,email',
        ]);

        /**
         * Check if mobile or email is entered + not already enabled.
         */

        $user = $this->user();

        if (!$user->{$request->type} || $user->{$request->type . '_is_verified'}) {

            return response()->json([
                'error' => __('messages.auth.otp.failed'),
            ], 400);

        }

        /**
         * Send OTP.
         */

        try {

            $this->OTP()->send($request->type, $user->{$request->type});

            return response()->json([
                'message' => __('messages.auth.otp.enterToken', ['destination' => $user->{$request->type}]),
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => __('messages.auth.otp.failed')
            ], 400);

        }
    }

    /**
     * @return OTPController
     */

    private function OTP(): OTPController
    {
        return new OTPController();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function verifyOTP(Request $request): JsonResponse
    {
        $this->validate($request, [
            'type' => 'required|in:mobile,email',
            'token' => 'required|numeric'
        ]);

        try {

            $this->OTP()->verify($request->token, $this->user()->{$request->type});

            $this->user()->update([
                $request->type . '_is_verified' => true,
            ]);

            return response()->json([
                'message' => __('messages.auth.otp.successful')
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => __('messages.auth.otp.wrongToken')
            ], 400);

        }

    }
}
