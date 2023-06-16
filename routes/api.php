<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

use App\Http\Controllers\Users\Kyc\KycController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * App status.
 */

Route::get('app-status', 'AppStatusController@status')->name('app-status');

/**
 * Privacy-policies
 */

Route::get('privacy-policies', 'PrivacyPolicyController@list')->name('privacy-policies.list');
Route::get('privacy-policies/{privacyPolicy}', 'PrivacyPolicyController@sow')->name('privacy-policies.show');

/**
 * Translations.
 */

Route::get('assets/download/translations', 'TranslationDownloadController@download')->name('translations');

/**
 * Public settings.
 */

Route::get('settings', 'SettingController@index')->name('public-settings');

/**
 * Check auth.
 */

Route::get('check-auth/{guard}', 'CheckAuthController@check')->name('check-auth');

/**
 * Users routes.
 */

Route::prefix('user')->namespace('Users')->name('user.')->middleware('2fa')->group(function () {

    /**
     * Auth.
     */

    Route::prefix('auth')->namespace('Auth')->name('auth.')->group(function () {

        /**
         * Socialite.
         */

        Route::post('oauth/redirect', 'OauthController@redirect')->name('oauth.redirect');
        Route::get('oauth/callback/{provider}', 'OauthController@callback')->name('oauth.callback');

        /**
         * Metamask.
         */

//        Route::prefix('ethereum')->name('ethereum.')->group(function (){
//            Route::get('message', 'EthereumController@getMessage')->name('get-message');
//            Route::post('message', 'EthereumController@verifyMessage')->name('verify-message');
//        });

        /**
         * Register.
         */

        Route::prefix('register')->name('register.')->group(function () {
            Route::post('step-one', 'RegisterController@stepOne')->name('stepOne');
            Route::post('step-two', 'RegisterController@stepTwo')->name('stepTwo');
            Route::post('step-three', 'RegisterController@stepThree')->name('stepThree');
            Route::post('resend-OTP', 'RegisterController@resendOTP')->name('resendOTP');
        });

        /**
         * Reset Password.
         */

        Route::prefix('reset-password')->name('reset-password.')->group(function () {
            Route::post('step-one', 'ResetPasswordController@stepOne')->name('stepOne');
            Route::post('step-two', 'ResetPasswordController@stepTwo')->name('stepTwo');
            Route::post('resend-OTP', 'ResetPasswordController@resendOTP')->name('resendOTP');
        });

        /**
         * Login.
         */

        Route::post('login', 'LoginController@login')->name('login');

        /**
         * Logout
         */

        Route::post('logout', 'LoginController@logout')->name('logout');

    });

    /**
     * Two-factor authentication.
     */

    Route::prefix('2fa')->name('2fa.')->group(function () {
        Route::post('enable', 'TwoFactorAuthController@enable')->name('enable');
        Route::post('disable', 'TwoFactorAuthController@disable')->name('disable');
        Route::post('verify', 'TwoFactorAuthController@verify')->name('verify');
        Route::post('send', 'TwoFactorAuthController@sendOTP')->name('sendOTP');
    });

    /**
     * Profile.
     */

    Route::namespace('Profile')->prefix('profile')->name('profile.')->group(function () {
        Route::get(null, 'ProfileController@list')->name('list');
        Route::match(['PUT', 'PATCH'], null, 'ProfileController@update')->name('update');
        Route::post('sendOTP', 'ProfileController@sendOTP')->name('sendOTP');
        Route::post('verifyOTP', 'ProfileController@verifyOTP')->name('verifyOTP');
    });

    /**
     * Change password.
     */

    Route::match(['PUT', 'PATCH'], 'change-password', 'Profile\PasswordController@update')->name('password.update');

    /**
     * Referrals.
     */

    Route::prefix('referrals')->namespace('Referral')->name('referrals.')->group(function () {
        Route::get(null, 'ReferralController@getLog')->name('get-log');
        Route::post(null, 'ReferralController@submitReferrer')->name('submit-referrer');
    });

    /**
     * Tickets.
     */

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get(null, 'TicketController@index')->name('index');
        Route::post('store', 'TicketController@store')->name('store');
        Route::get('{ticket}', 'TicketController@show')->name('show');
        Route::post('{ticket}', 'TicketController@answer')->name('answer');
        Route::delete('{ticket}', 'TicketController@delete')->name('delete');
    });

    /**
     * Wallets.
     */

    Route::prefix('wallets')->namespace('Wallet')->name('wallets.')->group(function () {
        Route::get(null, 'WalletController@list')->name('list');
        Route::get('get/{wallet}', 'WalletController@show')->name('show');
        Route::post('query', 'WalletController@query')->name('query');
        Route::get('transactions', 'WalletController@transactions')->name('transactions');
        Route::post('deposit-request', 'WalletController@depositRequest')->name('deposit-request');
        Route::match(['POST', 'GET'], 'deposit-call-back/{depositId}', 'WalletController@depositCallback')->name('deposit-callback');
        Route::post('deposit-call-back-validator', 'WalletController@depositCallbackValidator')->name('deposit-callback-validator');
        Route::get('manual-deposits', 'WalletController@manualDeposits')->name('manual-deposits');
        Route::post('manual-deposits', 'WalletController@manualDepositStore')->name('manual-deposit-store');
        Route::post('withdraw-request', 'WalletController@withdrawRequest')->name('withdraw-request');
        Route::post('withdraw-cancel/{withdraw}', 'WalletController@withdrawCancel')->name('withdraw-cancel');
        Route::post('withdrawable-assets', 'WalletController@withdrawableAssets')->name('withdrawable-assets');
        Route::post('virtual-assets/reset', 'WalletController@resetVirtualAssets')->name('reset-virtual-assets');
    });

    /**
     * Exchange routes.
     */

    Route::prefix('exchange')->name('exchange.')->namespace('Exchange')->group(function () {
        Route::get('markets', 'ExchangeController@markets')->name('markets');
        Route::get('market/candlestick-data/{market}/{timeframe}', 'ExchangeController@getCandles')->name('get-candles');
        Route::post('order', 'ExchangeController@storeOrder')->name('submit-order');
        Route::delete('order/{order}', 'ExchangeController@cancelOrder')->name('cancel-order');
        Route::delete('orders', 'ExchangeController@cancelAllOrders')->name('cancel-all-orders');
        Route::get('orders/{order}', 'ExchangeController@getOrder')->name('get-order');
        Route::get('orders', 'ExchangeController@getOrders')->name('get-orders');
        Route::post('query', 'ExchangeController@query')->name('query');
        Route::post('enable-market-broadcast', 'ExchangeController@enableMarketBroadcast')->name('enable-market-broadcast');
    });

    /**
     * Notifications.
     */

    Route::resource('notifications', 'NotificationController')->only('index', 'show');

    /**
     * Bank accounts.
     */

    Route::prefix('bank-accounts')->namespace('BankAccount')->name('bank-accounts.')->group(function (){
        Route::get(null, 'BankAccountController@list')->name('list');
        Route::get('{bankAccount}', 'BankAccountController@show')->name('show');
        Route::match(['PUT', 'PATCH'], '{bankAccount}', 'BankAccountController@update')->name('update');
        Route::post(null, 'BankAccountController@store')->name('store');
        Route::delete('{bankAccount}', 'BankAccountController@destroy')->name('destroy');
    });

    /**
     * Logs.
     */

    Route::get('logs', 'LogController@index')->name('logs.index');

    /**
     * Documents
     */

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get(null, 'DocumentController@index')->name('index');
        Route::post(null, 'DocumentController@store')->name('store');
    });

    /**
     * Kyc
     */
    Route::prefix('kyc')->namespace('Kyc')->name('kyc.')->middleware('auth:user')->group(function (){
        Route::post(null, [KycController::class, 'getAuthorizationKycInfo'])->name('get-authorization-kyc-info');
    });
});

/**
 * Admin Routes.
 */

Route::prefix('admin')->namespace('Admins')->name('admin.')->group(function () {

    /**
     * Auth.
     */

    Route::namespace('Auth')->prefix('auth')->name('auth.')->group(function () {

        /**
         * Login.
         */

        Route::post('login', 'LoginController@login')->name('login');

        /**
         * Logout.
         */

        Route::post('logout', 'LoginController@logout')->name('logout');

    });

    /**
     * Statistics.
     */

    Route::get('statistics', 'StatisticController@index')->name('statistics.index');

    /**
     * Roles.
     */

    Route::resource('roles', 'Role\RoleController')->except(['edit', 'create']);
    Route::get('roles/query/{guard}', 'Role\RoleController@query')->name('roles.query');

    /**
     * Admins.
     */

    Route::resource('admins', 'AdminController')->except('destroy', 'create', 'edit');
    Route::post('admins/reset-password', 'AdminController@resetPassword')->name('admins.reset-password');

    /**
     * Change password.
     */

    Route::match(['PUT', 'PATCH'], 'change-password', 'PasswordController@update')->name('password.update');

    /**
     * Tickets.
     */

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('require-attention-count', 'TicketController@requireAttentionCount')->name('require-attention-count');
        Route::get(null, 'TicketController@index')->name('index');
        Route::get('{ticket}', 'TicketController@show')->name('show');
        Route::post('{ticket}', 'TicketController@answer')->name('answer');
        Route::delete('{ticket}', 'TicketController@close')->name('close');
        Route::delete('delete/{ticket}', 'TicketController@delete')->name('delete');
    });

    /**
     * Documents.
     */

    Route::resource('documents', 'DocumentController')->except('create', 'edit');
    Route::prefix('documents/users/management')->name('documents.users.')->group(function () {
        Route::get('require-attention-count', 'UserDocumentController@requireAttentionCount')->name('require-attention-count');
        Route::get(null, 'UserDocumentController@index')->name('index');
        Route::get('{document}', 'UserDocumentController@show')->name('show');
        Route::match(['PUT', 'PATCH'], '{document}', 'UserDocumentController@update')->name('update');
        Route::get('download/{document}', 'UserDocumentController@download')->name('download');
    });

    /**
     * Wallet Addresses.
     */

    Route::prefix('wallet-addresses')->namespace('WalletAddress')->name('wallet-addresses.')->group(function () {
        Route::get(null, 'WalletAddressController@list')->name('list');
        Route::get('{walletAddress}', 'WalletAddressController@show')->name('show');
        Route::post(null, 'WalletAddressController@store')->name('store');
        Route::match(['PUT', 'PATCH'], '{walletAddress}', 'WalletAddressController@update')->name('update');
        Route::delete('{walletAddress}', 'WalletAddressController@destroy')->name('destroy');
    });

    /**
     * Notifications.
     */

    Route::prefix('notifications')->namespace('Notification')->name('notifications.')->group(function (){
        Route::get(null, 'NotificationController@list')->name('list');
        Route::post(null, 'NotificationController@store')->name('store');
    });

    /**
     * Users.
     */

    Route::resource('users', 'User\UserController')->only(['index', 'update', 'show', 'store']);

    /**
     * Transactions.
     */

    Route::prefix('transactions')->namespace('Transaction')->name('transactions.')->group(function () {
        Route::get('require-attention-count', 'TransactionController@requireAttentionCount')->name('require-attention-count');
        Route::get('orders/{order}', 'TransactionController@showOrder')->name('show.order');
        Route::post('query', 'TransactionController@query')->name('query');
        Route::post('withdraws', 'TransactionController@withdrawStore')->name('withdraws.store');
        Route::get('withdraws', 'TransactionController@getWithdraws')->name('withdraws.list');
        Route::match(['PUT', 'PATCH'], 'withdraws/{withdraw}', 'TransactionController@withdrawUpdate')->name('withdraws.update');
        Route::get('deposits', 'TransactionController@getDeposits')->name('deposits.list');
        Route::post('deposits', 'TransactionController@depositStore')->name('deposits.store');
        Route::get('manual-deposits', 'TransactionController@manualDeposits')->name('manual-deposits');
        Route::match(['PUT', 'PATCH'], 'manual-deposits/{manualDeposit}', 'TransactionController@manualDepositUpdate')->name('manual-deposit-update');
    });

    /**
     * Accountancy.
     */

    Route::prefix('accountancy')->namespace('Accountancy')->name('accountancies.')->group(function (){
        Route::get(null, 'AccountancyController@list')->name('list');
        Route::get('{accountancy}', 'AccountancyController@show')->name('show');
        Route::post(null, 'AccountancyController@store')->name('store');
    });


    /**
     * Settings.
     */

    Route::prefix('settings')->namespace('Setting')->name('settings.')->group(function () {
        Route::get(null, 'SettingController@index')->name('index');
        Route::match(['PUT', 'PATCH'], null . '/{group}', 'SettingController@update')->name('update');
        Route::post('maintenance-mode', 'SettingController@maintenanceMode')->name('maintenance-mode');
        Route::post('cache-clear', 'SettingController@cacheClear')->name('cache-clear');
        Route::post('appearance-restore', 'SettingController@appearanceRestore')->name('appearance-restore');
        Route::post('create-database-backup', 'SettingController@createDatabaseBackup')->name('create-database-backup');
        Route::get('database-backups', 'SettingController@databaseBackups')->name('database-backups');
        Route::get('database-backups/download/{backup}', 'SettingController@downloadDatabaseBackup')->name('download-database-backup');
    });

    /**
     * Exchanges.
     */

    Route::prefix('exchanges')->name('exchanges.')->group(function () {
        Route::post('rate-limits', 'ExchangeController@rateLimits')->name('rate-limits');
        Route::post('account', 'ExchangeController@account')->name('account');
        Route::post('assets', 'ExchangeController@assets')->name('assets');
        Route::post('all-orders', 'ExchangeController@allOrders')->name('all-orders');
    });

    /**
     * Blockchains.
     */

    Route::prefix('blockchains')->namespace('Blockchain')->name('blockchains.')->group(function () {
        Route::get(null, 'BlockchainController@list')->name('list');
        Route::get('{blockchain}', 'BlockchainController@show')->name('show');
        Route::post(null, 'BlockchainController@store')->name('store');
        Route::match(['PUT', 'PATCH'], '{blockchain}', 'BlockchainController@update')->name('update');
        Route::delete('{blockchain}', 'BlockchainController@destroy')->name('destroy');
    });

    /**
     * Symbols.
     */

    Route::prefix('symbols')->namespace('Symbol')->name('symbols.')->group(function () {
        Route::get(null, 'SymbolController@list')->name('list');
        Route::get('{symbol}', 'SymbolController@show')->name('show');
        Route::post(null, 'SymbolController@store')->name('store');
        Route::match(['PUT', 'PATCH'], '{symbol}', 'SymbolController@update')->name('update');
        Route::delete('{symbol}', 'SymbolController@destroy')->name('destroy');
    });

    /**
     * Markets.
     */

    Route::prefix('markets')->namespace('Market')->name('markets.')->group(function () {
        Route::get(null, 'MarketController@list')->name('list');
        Route::get('{market}', 'MarketController@show')->name('show');
        Route::post(null, 'MarketController@store')->name('store');
        Route::match(['PUT', 'PATCH'], '{market}', 'MarketController@update')->name('update');
        Route::delete('{market}', 'MarketController@destroy')->name('destroy');
    });

    /**
     * SMS
     */

    Route::prefix('sms')->name('sms.')->group(function () {
        Route::post('get-credit', 'SMSController@getCredit')->name('get-credit');
        Route::post('get-logs', 'SMSController@getLogs')->name('get-logs');
        Route::post('send-test', 'SMSController@sendTest')->name('send-test');
    });

    /**
     * Groups.
     */

    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get(null, 'GroupController@list')->name('list');
        Route::get('{group}', 'GroupController@show')->name('show');
        Route::post(null, 'GroupController@store')->name('store');
        Route::match(['PUT', 'PATCH'], '{group}', 'GroupController@update')->name('update');
        Route::delete('{group}', 'GroupController@destroy')->name('destroy');
    });

    /**
     * Privacy Policies.
     */

    Route::prefix('privacy-policies')->name('privacy-policies.')->group(function () {
        Route::get(null, 'PrivacyPolicyController@list')->name('list');
        Route::get('{privacyPolicy}', 'PrivacyPolicyController@show')->name('show');
        Route::post(null, 'PrivacyPolicyController@store')->name('store');
        Route::match(['PUT', 'PATCH'], '{privacyPolicy}', 'PrivacyPolicyController@update')->name('update');
        Route::delete('{privacyPolicy}', 'PrivacyPolicyController@destroy')->name('destroy');
    });

});
