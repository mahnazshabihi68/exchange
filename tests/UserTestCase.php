<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests;

use Laravel\Sanctum\Sanctum;

class UserTestCase extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs($this->user, [], 'user');
    }
}
