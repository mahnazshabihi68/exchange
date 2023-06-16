<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Admins\Role;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function __;
use function abort;
use function response;

class RoleController extends Controller
{
    /**
     * RoleController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:role']);
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'roles' => Role::whereGuardName('admin')->with('permissions')->latest()->get(),
            'permission' => Permission::latest()->get()
        ]);
    }

    /**
     * @param  Role  $role
     * @return JsonResponse
     */

    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'role' => $role->with('permissions')->findOrFail($role->id)
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|string|unique:roles',
            'permissions' => 'required|array',
        ]);

        $permissions = Permission::whereGuardName('admin')->whereIn('id', $request->permissions)->get();

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'admin'
            ]);

            $role->syncPermissions($permissions);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }


        return response()->json([
            'message' => __('messages.roles.store.successful')
        ]);
    }

    /**
     * @param  Request  $request
     * @param  Role  $role
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function update(Request $request, Role $role): JsonResponse
    {
        if ($role->id === 1) {
            abort(400);
        }

        $this->validate($request, [
            'name' => 'required|string|unique:roles,name,'.$role->id,
            'permissions' => 'required|array',
        ]);

        $permissions = Permission::whereGuardName('admin')->get()->whereIn('id', $request->permissions);

        DB::beginTransaction();
        try {
            $role->syncPermissions($permissions);

            $role->update([
                'name' => $request->name
            ]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }


        return response()->json([
            'message' => __('messages.roles.update.successful'),
        ]);
    }

    /**
     * @param  string  $guard
     * @return JsonResponse
     */

    public function query(string $guard): JsonResponse
    {
        return response()->json([
            'roles' => Role::whereGuardName($guard)->latest()->get(),
            'permissions' => Permission::whereGuardName($guard)->latest()->get()
        ]);
    }

    /**
     * @param  Request  $request
     * @param  Role  $role
     * @return JsonResponse
     * @throws Exception
     */

    public function destroy(Request $request, Role $role): JsonResponse
    {
        if ($role->id === 1) {
            abort(400);
        }

        $role->delete();

        return response()->json([
            'message' => __('messages.roles.destroy.successful'),
        ]);
    }
}
