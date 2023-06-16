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

class CheckAuthTest extends UserTestCase
{
    public function test_check_auth()
    {
        $response = $this->get(route('check-auth', 'user'));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'guard',
                'user',
                'permissions',
            ]);
    }
}
