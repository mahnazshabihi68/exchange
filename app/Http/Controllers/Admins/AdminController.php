<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins;

use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Jobs\SendSMS;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * AdminController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:admin']);
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'admins' => Admin::with('roles.permissions')->latest()->get(),
        ]);
    }

    /**
     * @param Admin $admin
     * @return JsonResponse
     */

    public function show(Admin $admin): JsonResponse
    {
        return response()->json([
            'admin' => $admin->with([
                'roles.permissions',
                'logs'
            ])->findOrFail($admin->id)
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:admins',
            'mobile' => 'required|ir_mobile|unique:admins',
            'password' => ['required', 'string', 'confirmed', 'min:10', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'roles' => 'required|array',
            'avatar' => 'image|max:2048'
        ]);

        $data = $request->except(['avatar', 'roles', 'password']);

        $data['password'] = bcrypt($request->password);

        DB::beginTransaction();
        try {
            if ($request->has('avatar')) {

                $data['avatar'] = $request->file('avatar')->store('avatars');

                Image::make(public_path('storage/' . $data['avatar']))->fit(128, 128)->save();

            }

            $admin = Admin::create($data);

            $roles = Role::whereIn('id', $request->roles)->whereGuardName('admin')->get();

            $admin->assignRole($roles);

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.admins.store.successful')
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }

    }

    /**
     * @param Request $request
     * @param Admin $admin
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function update(Request $request, Admin $admin): JsonResponse
    {
        /**
         * Validate request.
         */

        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'mobile' => 'required|ir_mobile|unique:admins,mobile,' . $admin->id,
            'roles' => 'required|array',
            'avatar' => 'image|max:2048'
        ]);

        $data = $request->except(['roles', 'avatar']);

        /**
         * Handle avatar.
         */
        Db::beginTransaction();
        try {
            if ($request->has('avatar')) {

                $data['avatar'] = $request->file('avatar')->store('avatars');

                Image::make(public_path('storage/' . $data['avatar']))->fit(128, 128)->save();

                /**
                 * Delete previous avatar.
                 */

                if ($admin->avatar != 'avatar.png' && file_exists(public_path('storage/' . $admin->avatar))) {

                    unlink(public_path('storage/' . $admin->avatar));

                }

            }

            /**
             * Update roles.
             */

            $roles = Role::whereIn('id', $request->roles)->whereGuardName('admin')->get();

            $admin->syncRoles($roles);

            $admin->update($data);

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.admins.update.successful'),
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function resetPassword(Request $request): JsonResponse
    {
        try {

            /**
             * Validate request.
             */

            $this->validate($request, [
                'admin_id' => 'required|numeric|exists:admins,id'
            ]);

            /**
             * Fetch admin.
             */

            $admin = Admin::find($request->admin_id);
            if(!$admin instanceof Admin){
                throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
            }

            /**
             * Update password.
             */

            $newPassword = Str::random(5) . '@' . rand(10000, 99999);

            $admin->update([
                'password' => bcrypt($newPassword)
            ]);

            /**
             * Send sms.
             */

            $data = [
                [
                    "Parameter" => "NewPassword",
                    "ParameterValue" => $newPassword
                ],
                [
                    "Parameter" => "PlatformName",
                    "ParameterValue" => config('settings.name_' . app()->getLocale())
                ],
            ];

            SendSMS::dispatch($admin->mobile, env('SMS_NEWPASSWORD'), $data);

            /**
             * Return.
             */

            return response()->json([
                'message' => __('messages.profile.password.successful')
            ]);

        } catch (\Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }

    }
}
