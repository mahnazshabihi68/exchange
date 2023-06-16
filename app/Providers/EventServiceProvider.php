<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Providers;

use App\Events\Log\FileLogIsNeeded;
use App\Events\Log\MongoDBLogIsNeeded;
use App\Events\Log\MySQLLogIsNeeded;
use App\Listeners\Log\LogToFile;
use App\Listeners\Log\LogToMongoDB;
use App\Listeners\Log\LogToMySQL;
use App\Listeners\LogoutListener;
use App\Models\Kyc;
use App\Models\Order;
use App\Models\Symbol;
use App\Models\Trade;
use App\Models\User;
use App\Observers\KycObserver;
use App\Observers\OrderObserver;
use App\Observers\SymbolObserver;
use App\Observers\TradeObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        Logout::class => [
            LogoutListener::class,
        ],
        MySQLLogIsNeeded::class => [
            LogToMySQL::class,
        ],
        MongoDBLogIsNeeded::class => [
            LogToMongoDB::class,
        ],
        FileLogIsNeeded::class => [
            LogToFile::class
        ],
    ];

    public function boot()
    {
        Order::observe(OrderObserver::class);

        User::observe(UserObserver::class);

        Symbol::observe(SymbolObserver::class);

        Trade::observe(TradeObserver::class);

        Kyc::observe(KycObserver::class);
    }
}
