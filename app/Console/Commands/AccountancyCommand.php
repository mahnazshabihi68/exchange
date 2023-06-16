<?php

namespace App\Console\Commands;

use App\Models\Accountancy;
use Illuminate\Console\Command;

class AccountancyCommand extends Command
{
    protected $signature = 'accountancy:run';

    protected $description = 'Runs accountancy function.';

    public function handle()
    {
        try {

            (new Accountancy())->createCumulativeAccountancy();

            $this->info($this->signature . ' has been lunched successfully at '. now());

        } catch (\Exception $exception){

            $this->error($exception->getMessage());

        }
    }
}
