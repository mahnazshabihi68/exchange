<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Providers;

use App\Models\Blockchain;
use App\Repositories\impls\BankAccountRepository;
use App\Repositories\impls\KycRepository;
use App\Repositories\impls\NotificationRepository;
use App\Repositories\impls\UserProfileRepository;
use App\Repositories\impls\UserRepository;
use App\Repositories\interfaces\IBankAccountRepository;
use App\Repositories\interfaces\IKycRepository;
use App\Repositories\interfaces\INotificationRepository;
use App\Repositories\interfaces\IUserProfileRepository;
use App\Repositories\interfaces\IUserRepository;
use App\Services\impls\BankAccountService;
use App\Services\impls\KycService;
use App\Services\impls\NotificationService;
use App\Services\impls\UserProfileService;
use App\Services\impls\UserService;
use App\Services\interfaces\IBankAccountService;
use App\Services\interfaces\IKycService;
use App\Services\interfaces\INotificationService;
use App\Services\interfaces\IUserProfileService;
use App\Services\interfaces\IUserService;
use App\Webservices\AtipayWithdraw\impls\AtipayWithdrawService;
use App\Webservices\AtipayWithdraw\interfaces\IAtipayWithdrawService;
use App\Webservices\CryptoWithdraw\impls\CryptoWithdrawService;
use App\Webservices\CryptoWithdraw\interfaces\ICryptoWithdrawService;
use App\Webservices\KycAuthorization\impls\KycAuthorizationService;
use App\Webservices\KycAuthorization\interfaces\IKycAuthorizationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(IKycService::class, KycService::class);
        $this->app->bind(IKycRepository::class, KycRepository::class);
        $this->app->bind(INotificationService::class, NotificationService::class);
        $this->app->bind(INotificationRepository::class, NotificationRepository::class);
        $this->app->bind(IKycAuthorizationService::class, KycAuthorizationService::class);
        $this->app->bind(IBankAccountService::class, BankAccountService::class);
        $this->app->bind(IBankAccountRepository::class, BankAccountRepository::class);
        $this->app->bind(IUserProfileService::class, UserProfileService::class);
        $this->app->bind(IUserProfileRepository::class, UserProfileRepository::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(ICryptoWithdrawService::class, CryptoWithdrawService::class);
        $this->app->bind(IAtipayWithdrawService::class, AtipayWithdrawService::class);
    }

    public function boot()
    {
        Validator::extend('address_is_valid', function ($attribute, $value, $parameters) {

            $blockchain = match (is_numeric($parameters[0])) {
                true => Blockchain::find($parameters[0]),
                false => Blockchain::title($parameters[0])->firstOrFail(),
            };

            return $blockchain->getExplorer()->setBlockchain($blockchain->title)->setAddress($value)->addressIsValid();
        });

        if (Schema::hasTable('settings')) {

            /**
             * Load and cache settings.
             */

            $settings = cache()->rememberForever('settings', function () {

                return DB::table('settings')->pluck('value', 'key');

            });

            /**
             * Push settings into config() helper.
             */

            foreach ($settings as $key => $value) {

                config()->set('settings.' . $key, $value);

            }

            foreach (['google', 'github'] as $provider) {

                config()->set('services.' . $provider . '.client_id', config('settings.' . $provider . '_api_key'));

                config()->set('services.' . $provider . '.client_secret', config('settings.' . $provider . '_secret_key'));

            }

        }

        /**
         * Broadcasting websocket config
         */

        if (App::environment('production', 'stage', 'local')) {
            \URL::forceScheme('https');
        }

//        $pusherScheme = Str::startsWith(env('APP_URL'), 'https') ? 'https' : 'http';
//
//        config()->set('broadcasting.connections.pusher.options.host', Str::after(config('app.url'), '://'));
//
//        config()->set('broadcasting.connections.pusher.options.scheme', $pusherScheme);
//
//        $pusherScheme == 'https' ? config()->set('broadcasting.connections.pusher.options.useTLS', true) : config()->set('broadcasting.connections.pusher.options.useTLS', false);

        /**
         * Lets define app version.
         */

        cache()->rememberForever('app-version', function () {
            return exec('git tag') ?? '1.0.0';
        });

        cache()->rememberForever('git', function () {
            return collect([
                'branch' => shell_exec('git rev-parse --abbrev-ref HEAD'),
                'tag' => exec('git tag') ?? '1.0.0',
                'latest_commit_hash' => shell_exec('git log --pretty=format:"%h" -n 1'),
                'latest_commit_time' => shell_exec('git log -1 --format=%ci')
            ]);
        });
    }
}
