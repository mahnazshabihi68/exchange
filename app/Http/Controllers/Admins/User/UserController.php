<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Admins\User;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\User\Store;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:user']);
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'users' => User::latest()->get()
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => $user->with([
                'permissions',
                'documents',
                'withdraws',
                'deposits',
                'bankAccounts',
                'orders',
                'wallets',
                'wallets.symbol',
                'walletAddresses',
                'logs',
                'groups'
            ])->findOrFail($user->id)
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function update(Request $request, User $user): JsonResponse
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'nullable|string',
            'national_code' => 'nullable|string|ir_national_code|unique:users,national_code,' . $user->id,
            'birthday' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|ir_mobile|unique:users,mobile,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
            'permissions' => 'nullable|array',
            'groups' => 'nullable|array'
        ]);

        $data = $request->except(['avatar', 'permissions', 'groups']);

        if ($user->mobile != $request->mobile) {

            $data['mobile_is_verified'] = false;

        }

        if ($user->email != $request->email) {

            $data['email_is_verified'] = false;

        }

        DB::beginTransaction();
        try{
            if ($request->hasFile('avatar')) {

                /**
                 * Store avatar.
                 */

                $data['avatar'] = $request->file('avatar')->store('avatars');

                Image::make(public_path('storage/' . $data['avatar']))->fit(128, 128)->save();

                /**
                 * Delete previous avatar.
                 */

                if ($user->avatar != 'avatar.png' && file_exists(public_path('storage/' . $user->avatar))) {

                    unlink(public_path('storage/' . $user->avatar));

                }

            }

            /**
             * Sync permissions.
             */

            $permissions = Permission::whereIn('id', $request->permissions)->whereGuardName('user')->get();

            $user->syncPermissions($permissions);

            /**
             * Sync groups.
             */

            if ($request->groups) {

                $groups = Group::whereIn('id', $request->groups)->get();

                $user->groups()->sync($groups);

            }

            /**
             * Update user.
             */

            $user->update($data);

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.users.update.successful'),
            ]);
        }
        catch(Exception $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Store $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function store(Store $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Define default data.
             */

            $data = $request->except(['avatar', 'groups', 'permissions', 'password']);

            $data['password'] = bcrypt($request->password);

            /**
             * Create new user.
             */

            $user = User::create($data);

            /**
             * Handle groups.
             */

            if ($request->groups) {

                $groups = Group::whereIn('id', $request->groups)->get();

                $user->groups()->sync($groups);

            }

            /**
             * handle permissions.
             */

            $permissions = Permission::whereIn('id', $request->permissions)->whereGuardName('user')->get();

            $user->syncPermissions($permissions);

            /**
             * Handle avatar.
             */

            if ($request->hasFile('avatar')) {

                /**
                 * Store avatar.
                 */

                $data['avatar'] = $request->file('avatar')->store('avatars');

                Image::make(public_path('storage/' . $data['avatar']))->fit(128, 128)->save();

                /**
                 * Delete previous avatar.
                 */

                if ($user->avatar != 'avatar.png' && file_exists(public_path('storage/' . $user->avatar))) {

                    unlink(public_path('storage/' . $user->avatar));

                }

            }
            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.users.store.successful'),
                'user' => $user
            ], 201);

        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}
