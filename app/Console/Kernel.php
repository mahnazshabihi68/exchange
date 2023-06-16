<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Console;

use App\Console\Commands\AccountancyCommand;
use App\Console\Commands\BitcoinDepositsCommand;
use App\Console\Commands\BlockchainExploreCommand;
use App\Console\Commands\CallbackWithdrawFiatCommand;
use App\Console\Commands\KycAuthorizationCommand;
use App\Console\Commands\MarketUpdateCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */

    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command(MarketUpdateCommand::class)->everyMinute()->withoutOverlapping();

        $schedule->command(BlockchainExploreCommand::class)->everyMinute()->withoutOverlapping();

        $schedule->command(AccountancyCommand::class)->hourly()->withoutOverlapping();

        $schedule->command(KycAuthorizationCommand::class)->everyTwoHours()->withoutOverlapping();

        $schedule->command(CallbackWithdrawFiatCommand::class)->everyTwoHours()->withoutOverlapping();

        $schedule->command('backup:run --only-db')->hourly()->withoutOverlapping();

        $schedule->command('backup:clean')->daily()->withoutOverlapping();

        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
