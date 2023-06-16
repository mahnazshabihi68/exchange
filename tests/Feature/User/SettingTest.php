<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\User;

use Tests\UserTestCase;

class SettingTest extends UserTestCase
{
    public function public_setting()
    {
        $response = $this->get(route('public-settings'));

        $response->assertSuccessful()->assertJsonStructure([
            'settings'
        ]);
    }
}
