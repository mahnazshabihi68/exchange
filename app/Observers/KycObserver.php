<?php

namespace App\Observers;

use Illuminate\Support\Facades\Lang;

class KycObserver
{
    public function updated($kyc)
    {
        $kyc->user()->first()
        ->notifications()
        ->create([
            'title' =>  Lang::get('messages.kyc.status'),
            'content' => json_decode($kyc->details)->message,
            'is_highlighted' => 1
        ]);
    }
}
