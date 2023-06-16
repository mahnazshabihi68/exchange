<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Referral;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Referral\Submit;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class ReferralController extends Controller
{
    /**
     * ReferralController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:user']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function getLog(): JsonResponse
    {
        try {

            return response()->json([
                'referrals' => $this->user()->referrals()->select(['first_name', 'last_name', 'avatar'])->latest()->get(),
                'referrer' => $this->user()->referrer()->exists() ? $this->user()->referrer()->first()->username : null,
                'rewards' => $this->user()->transactions()->type(1)->get()->groupBy('symbol.title')->map(function ($item, $key) {
                    return $item->sum('quantity');
                })
            ]);

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
     * @param Submit $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function submitReferrer(Submit $request): JsonResponse
    {
        try {

            if ($this->user()->referrer()->exists()) {

                throw new Exception(__('messages.referrals.submitReferrer.alreadySubmitted'));

            }

            $referrer = User::where('username', $request->username)->where('id', '!=', $this->user()->id);

            if (!$referrer->exists()) {

                throw new Exception(__('messages.referrals.submitReferrer.wrongReferralToken'));

            }

            $this->user()->referrer()->associate($referrer->first())->save();

            return response()->json([
                'message' => __('messages.referrals.submitReferrer.successful'),
            ]);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }
}
