<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin']);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'confirmed', 'min:10', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);

        /**
         * Check current password validation.
         */

        $currentPasswordCheck = Hash::check($request->current_password, $this->user()->password);

        if (!$currentPasswordCheck) {

            return response()->json([
                'error' => __('messages.profile.password.currentPasswordIsWrong')
            ], 400);

        }

        /**
         * Check whether current and new passwords are same or not.
         */

        $samePasswordsCheck = strcmp($request->password, $request->current_password);

        if ($samePasswordsCheck === 0) {

            return response()->json([
                'error' => __('messages.profile.password.samePasswordError')
            ], 400);

        }

        /**
         * Everything seems to be ok.
         */

        /**
         * Change password.
         */

        $this->user()->update([
            'password' => bcrypt($request->password),
        ]);

        /**
         * Return response.
         */

        return response()->json([
            'message' => __('messages.profile.password.successful')
        ]);
    }

    /**
     * @return Authenticatable
     */

    private function user(): Authenticatable
    {
        return auth('admin')->user();
    }
}
