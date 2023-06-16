<?php

namespace App\Observers;

use App\Models\Symbol;
use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user)
    {
        $user->username = Str::random(6);
    }

    public function created(User $user)
    {
        /**
         * Create Wallets.
         */

        foreach (Symbol::latest()->get() as $symbol){

            $user->setWallet($symbol->title, 1, false);

        }

        /**
         * USDT virtual asset.
         */

        $virtualUSDTQty = config('settings.virtual_USDT_wallet_default_amount');

        if (!$virtualUSDTQty > 0) {

            return;

        }

        $user->setWallet('USDT', 1, true)->chargeWallet($virtualUSDTQty);
    }
}
