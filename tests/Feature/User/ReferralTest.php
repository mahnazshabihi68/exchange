<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\User;

use App\Models\User;
use Tests\UserTestCase;

class ReferralTest extends UserTestCase
{
    public function test_get_reffered_users()
    {
        $response = $this->get(route('user.referrals.referred-users'));

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'referrals',
            'referrer',
            'rewards',
        ]);
    }

    public function test_can_reffer_user()
    {
        $user = User::where('id', '!=', $this->user->id)->get()->random();

        $this->assertEmpty($user->referrals);

        $response = $this->post(route('user.referrals.submit-referrer'), [
            'username' => $user->username,
        ]);

        $this->assertEquals(1, $user->referrals()->count());

        $response->assertSuccessful();
    }
}
