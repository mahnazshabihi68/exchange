<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Console\Commands;

use App\Jobs\OrderUpdate;
use App\Models\Order;
use Illuminate\Console\Command;

class OrderCommand extends Command
{
    protected $signature = 'orders:update';

    protected $description = 'Updates orders.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info($this->signature . ' had been started at ' . now());

        while (Order::isActive()->count() > 0) {

            OrderUpdate::dispatch();

            $this->info('dispatched successfully at ' . now());

            sleep(1);

        }

    }
}
