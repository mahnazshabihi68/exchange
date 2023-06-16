<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {

        /**
         * Defining default settings.
         */

        $settingsData = [
            'seo-setting' => [
                'name_fa' => 'صرافی',
                'name_en' => 'Exchange',
                'description_fa' => 'متاتگ توضیحات پروژه صرافی',
                'description_en' => 'Exchange projects description meta tag',
                'keywords_fa' => 'صرافی،خرید و فروش بیت کوین،بیت کوین،اتریوم،خرید و فروش اتریوم',
                'keywords_en' => 'Exchange,Cryptocurrency,Bitcoin,Ethereum',
                'additional_js' =>  null,
            ],
            'appearance-setting' => [
                'logo' => 'logo.png',
                'favicon' => 'favicon.png',
                'auth_background_picture' => 'auth_background.jpeg',
                'theme' => serialize(json_decode(file_get_contents(public_path('appearances/colors.json')), true)),
            ],
            'social-setting' => [
                'instagram' => null,
                'telegram' => null,
                'twitter' => null,
                'whatsapp' => null,
                'phone' => null,
            ],
            'api-setting' => [
                'smsir_api_key' => '6ee20933606a25940561344',
                'smsir_secret_key' => '4TwU}zj9wvW@8JB',
                'etherscan_api_key' => 'XM7BWJF9KEGBRAXN9GN9Z5AX6D9GHIRXZX',
                'google_api_key' => '280066107325-jbv2cuuum38b6hbvtopvdm4i4f2f9ssi.apps.googleusercontent.com',
                'google_secret_key' => 'i6NtcgCkS8xG4vEqo4-ZMSzy',
                'github_api_key' => '3b6cf511666c1d2cd659',
                'github_secret_key' => '3191c96da5afe62ed5139c101f9f1dfeb9f0bdb0',
                'binance_api_key' => 'vZzRIlUwSdmolK8SZLI9VV7lBgMW0rertdBeQQckYuW7VaghuHwFeuSEDJk4IF25',
                'binance_secret_key' => 'PbGHd4LEHuiXwdZ1UYpOdI3wdwYDuD39DUAfJtguP2Sldozy7ygYZU8KlEgCDIFM',
                'payping_api_key' => 'b75a9a1c2fecac514183b5ad6991907bc7bc8e72117fc341c739ee449853746d',
                'nobitex_username' => 'ali@khedmati.ir',
                'nobitex_password' => '9uRK#^ExDV^wtF$z#x@y6%Se8d&h*$3h',
                'atipay_api_key' => '489bc7cc-2256-49a6-b243-42343a1f26eb',
            ],
            'financial-setting' => [
                'accepted_order_types' => serialize(['LIMIT', 'MARKET']),
                'all_order_types' => serialize(['LIMIT', 'MARKET', 'STOPLOSSLIMIT', 'OTC']),
                'trade_wage' => 0.1,
                'referral_reward' => 20,
                'bankAccount_cardNumber' => '6219861052500591',
                'bankAccount_accountNumber' => '8258001223456',
                'bankAccount_shebaNumber' => 'IR530180000000003642908536',
                'bankAccount_bankName' => 'سامان',
                'bankAccount_ownerName' => 'تست',
                'BTC_address' => 'bc1qhl79xmg50l0kd3m94d4qewxyhzpxd2jngsxnhp',
                'ETH_address' => '0xa33B17d671968eAcc488726554e0065864198486',
                'TRX_address' => 'TRoWGus9GSwLm3EkqFyy6z834q5yGFNhNq',
                'trade_wage_receiver_user_id' => 1,
                'virtual_USDT_wallet_default_amount' => 100000,
                'deallocating_wallets_after_hours' => 120,
                'crypto_deposit_method' => 1,
                'irt_deposit_gateway_is_enabled' => true,
                'irt_deposit_gateway' => 'payping',
                'irt_deposit_min_amount' => 1000,
                'irt_deposit_max_amount' => 100000000
            ],
        ];

        /**
         * Insert into database.
         */

        foreach ($settingsData as $group => $settings) {

            foreach ($settings as $key => $value) {

                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'group' => $group
                ]);

            }

        }
    }
}
