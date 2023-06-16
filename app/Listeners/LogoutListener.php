<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Listeners;

use Jenssegers\Agent\Agent;

class LogoutListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $browser = $this->agent()->browser();

        $browserVersion = $this->agent()->version($browser);

        $OS = $this->agent()->platform();

        $OSVersion = $this->agent()->version($OS);

        $event->user->logs()->create([
            'event' => 0,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'device' => $this->agent()->deviceType(),
            'browser' => $browser . ' ' . $browserVersion,
            'OS' => $OS . ' ' . $OSVersion
        ]);
    }

    /**
     * @return Agent
     */

    private function agent(): Agent
    {
        return new Agent();
    }
}
