<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Group\Store;
use App\Http\Requests\Admins\Group\Update;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:group']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {

            return response()->json([
                'groups' => Group::with('users')->latest()->get()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ]);

        }
    }

    /**
     * @param Group $group
     * @return JsonResponse
     * @throws \JsonException
     */

    public function show(Group $group): JsonResponse
    {
        try {

            return response()->json([
                'group' => $group->with('users')->findOrFail($group->id)
            ]);

        } catch (Exception $exception) {
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
             * Create new group.
             */

            $group = Group::create($request->only('name'));

            /**
             * Associate users with group.
             */

            $users = User::whereIn('id', $request->users)->get();

            $group->users()->sync($users);

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.groups.store.successful'),
                'group' => $group
            ], 201);

        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Update $request
     * @param Group $group
     * @return JsonResponse
     * @throws \JsonException
     */

    public function update(Update $request, Group $group): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Update group.
             */

            $group->update($request->only('name'));

            /**
             * Associate users with group.
             */

            $users = User::whereIn('id', $request->users)->get();

            $group->users()->sync($users);

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.groups.update.successful'),
                'group' => $group
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Group $group
     * @return JsonResponse
     * @throws \JsonException
     */

    public function destroy(Group $group): JsonResponse
    {
        try {

            $group->delete();

            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.groups.destroy.successful')
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }
}
