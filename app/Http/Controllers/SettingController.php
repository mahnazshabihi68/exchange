<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Helpers\Util;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{

    protected array $publicFields;

    /**
     * SettingController constructor.
     */

    public function __construct()
    {

        /**
         * Set publicFields.
         */

        $this->publicFields = [
            'name_fa',
            'name_en',
            'description_fa',
            'description_en',
            'logo',
            'favicon',
            'additional_js',
            'bankAccount_cardNumber',
            'bankAccount_accountNumber',
            'bankAccount_shebaNumber',
            'bankAccount_bankName',
            'bankAccount_ownerName',
            'BTC_address',
            'ETH_address',
            'TRX_address',
            'instagram',
            'telegram',
            'twitter',
            'whatsapp',
            'phone',
            'auth_background_picture',
            'crypto_deposit_method',
            'irt_deposit_gateway_is_enabled',
            'irt_deposit_min_amount',
            'irt_deposit_max_amount',
            'theme',
            'accepted_order_types'
        ];
    }

    public function index(): JsonResponse
    {
        /**
         * Fetch settings.
         */

        $settings = cache()->rememberForever('public-settings', function () {

            $settings = DB::table('settings')
                ->whereIn('key', $this->publicFields)
                ->pluck('value', 'key')
                ->put('app_version', cache('app-version'))
                ->put('copy_right_fa', verta()->format('Y') . ' | توسعه داده شده توسط مجموعه ورنا')
                ->put('copy_right_en', now()->format('Y') . ' | Developed by Vorna Software Company.');

            foreach ($settings as $key => $value) {

                try {

                    if (is_null($value)) {

                        continue;

                    }

                    $settings[$key] = unserialize($value);

                } catch (Exception $exception) {
                    Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
                    continue;

                }

            }

            return $settings;

        });

        /**
         * Return response.
         */

        return response()->json([
            'settings' => $settings
        ]);
    }

}
