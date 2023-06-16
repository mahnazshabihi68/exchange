<?php

namespace App\Observers;

use App\Models\Symbol;
use App\Models\User;

class SymbolObserver
{
    public function create(Symbol $symbol)
    {
        /**
         * Create wallet for each user.
         */

        foreach (User::all() as $user){

            $user->setWallet($symbol->id, 1, false);

        }
    }
}
