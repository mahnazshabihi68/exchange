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
use App\Models\Log;
use App\Models\User;
use App\Models\WalletAddress;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class StatisticController extends Controller
{
    /**
     * StatisticController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:admin']);
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */

    public function index(): JsonResponse
    {

        /**
         * Statistics.
         */

         $statistics = cache()->remember('admin_statistics', now()->addMinutes(15), function () {

            /**
             * Define @Collection instance.
             */

            $data = new Collection([
                'updated_at' => now()
            ]);

            /******************************************
             * Users statistics.
             ******************************************/

            $users = [
                'count' => User::count(),
            ];

            /**
             * Chart past 30 days.
             */

            for ($i = 0; $i < 30; $i++) {

                $users['charts']['logins'][today()->subDays($i)->toDateString()] = Log::event(1)->whereHasMorph('loggable', User::class)->whereBetween('created_at', [today()->subDays($i), today()->subDays($i - 1)])->distinct('ip', 'loggable_id')->count();

                $users['charts']['registers'][today()->subDays($i)->toDateString()] = User::whereBetween('created_at', [today()->subDays($i), today()->subDays($i - 1)])->count();

            }

            $data->put('users', $users);

            /****************************************
             * Wallet addresses statistics.
             ****************************************/

            $WalletAddresses = WalletAddress::isActive()->get()->groupBy('blockchain.title')->map(fn($wallets) => collect($wallets)->countBy('is_available'))->toArray();

            $data->put('WalletAddresses', $WalletAddresses);

            /**
             * Return data.
             */

            return $data;

        });

        /**
         * Server status.
         */

        $serverStatus = [
            'app-version' => exec('git tag') ?? '1.0.0',
            'laravel-version' => app()->version(),
            'php-version' => Str::between(shell_exec('php --version'), 'PHP ', ' (cli)') ?? 0,
            'mysql-version' => Str::between(shell_exec('mysql --version'), 'mysql  Ver ', ', for') ?? 0,
            'redis-version' => Str::after(shell_exec('redis-cli --version'), 'redis-cli '),
            'horizon-status' =>  shell_exec(PHP_BINDIR.'/php ' . base_path() . '/artisan horizon:status'),
            'total-memory' => $this->memoryUsage()->total,
            'used-memory' => $this->memoryUsage()->used,
            'cpu-load-average' => $this->loadAverage(),
            'cpu-cores' => shell_exec('cat /proc/cpuinfo | grep processor | wc -l') ?? 0,
            'uptime' => Str::after(shell_exec('uptime -p'), 'up ') ?? 0,
            'total-storage' => number_format(disk_total_space('/') / pow(1024, 3), 2) . ' GB' ?? 0,
            'used-storage' => number_format((disk_total_space('/') - disk_free_space('/')) / pow(1024, 3), 2) . ' GB' ?? 0,
        ];

        /**
         * Return response.
         */

        return response()->json([
            'statistics' => $statistics,
            'server-status' => $serverStatus
        ]);
    }


    /**
     * @return object
     */

    private function memoryUsage(): object
    {
        $free = shell_exec('free -m');

        $output = (object)[
            'total' => 0,
            'used' => 0,
        ];

        if (!$free) {

            return $output;

        }

        $free = trim($free);

        $free_arr = explode("\n", $free);

        $mem = explode(" ", $free_arr[1]);

        $mem = array_filter($mem);

        $mem = array_merge($mem);

        return (object)[
            'total' => number_format($mem[1]) . ' MB',
            'used' => number_format($mem[2]) . ' MB',
        ];
    }

    /**
     * @return float
     */

    private function loadAverage(): float
    {
        if (!function_exists('sys_getloadavg')) {

            return 0;

        }

        $load = sys_getloadavg();

        return $load[0];
    }
}
