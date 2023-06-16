<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\Admin;

use Hash;
use Tests\AdminTestCase;

class ChangePasswordTest extends AdminTestCase
{
    public function test_can_change_password()
    {
        $pwd = 'secret';

        $this->admin->forceFill([
            'password' => bcrypt($pwd),
        ])->save();

        $response = $this->put(route('admin.password.update'), [
            'current_password' => $pwd,
            'password' => $newPwd = (string)rand(111111111, 999999988888),
            'password_confirmation' => $newPwd,
        ]);

        $response->assertSuccessful();

        $this->admin->fresh();

        $this->assertTrue(Hash::check($newPwd, $this->admin->password));
    }
}
