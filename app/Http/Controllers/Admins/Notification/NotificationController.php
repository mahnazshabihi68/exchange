<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Admins\Notification;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Notification\Store;
use App\Models\Group;
use App\Models\Notification;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use function __;
use function response;

class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
     */
    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:notification']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {

            return response()->json([
                'notifications' => Notification::latest()->get(),
            ]);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
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

        try {

            /**
             * Fetch request.
             */

            $notification = $request->except(['send_method', 'users', 'groups']);

            /**
             * Users.
             */

            $userIds = [];

            if ($request->send_method === 'users') {

                foreach ($request->users as $user) {

                    $userIds[] = $user;

                }

            } elseif ($request->send_method === 'groups') {

                foreach ($request->groups as $group) {

                    $group = Group::whereId($group)->first();

                    if (!$group) {

                        continue;

                    }

                    foreach ($group->users()->get() as $user) {

                        $userIds[] = $user->id;

                    }
                }
            }

            /**
             * Create notification for users.
             */

            foreach ($userIds as $user){

                $user = User::find($user);

                if (!$user){

                    continue;

                }

                $user->notifications()->create($notification);

            }

            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.notifications.store.successful'),
            ]);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }
}
