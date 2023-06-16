<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Contracts\HasApiTokens;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    protected $user;

    protected $seed = true;

    protected $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->withHeader('Accept', 'application/json');
    }

    protected function apiActingAs(HasApiTokens $user)
    {
        $token = $user->createToken('access-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token);
    }
}
