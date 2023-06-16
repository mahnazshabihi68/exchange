<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;
use PDO;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment(['local', 'testing'])) {
            $connection = config('database.default');
            if ($connection == 'mysql') {
                $config = config('database.connections.mysql');
                try {

                    new PDO(sprintf("mysql:dbname=%s;host=%s;port=%s", $config['database'], $config['host'], $config['port']), $config['username'], $config['password']);
                } catch (Exception $e) {
                    config(['database.connections.mysql.host' => '127.0.0.1']);
                }
            }
        }
    }
}
