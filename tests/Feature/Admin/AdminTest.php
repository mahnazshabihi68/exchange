<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\Admin;

use App\Jobs\SendSMS;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Storage;
use Tests\AdminTestCase;

class AdminTest extends AdminTestCase
{
    public function test_no_permission_see_admins()
    {
        $response = $this->get(route('admin.admins.index'));

        $response->assertForbidden();
    }

    public function test_can_see_admins()
    {
        $this->admin->givePermissionTo('admin');

        $response = $this->get(route('admin.admins.index'));

        $response->assertSuccessful()
            ->assertJsonStructure(['admins']);
    }

    public function test_can_see_admin()
    {
        $this->admin->givePermissionTo('admin');

        $response = $this->get(route('admin.admins.show', $admin = Admin::first()));

        $response->assertSuccessful()
            ->assertJsonStructure(['admin']);
    }

    public function test_no_permission_create_admin()
    {
        $count = Admin::count();

        $response = $this->post(route('admin.admins.store'), $this->fakeData());

        $response->assertForbidden();

        $this->assertEquals($count, Admin::count());
    }

    private function fakeData()
    {
        Storage::fake();

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'mobile' => '09126667090',
            'password' => $pswd = $this->faker->password(),
            'password_confirmation' => $pswd,
            'roles' => Role::pluck('id')->toArray(),
            // 'avatar'                        =>  UploadedFile::fake()->image('photo2.jpg'),
        ];
    }

    public function test_can_create_admin()
    {
        $count = Admin::count();

        $this->admin->givePermissionTo('admin');

        $response = $this->post(route('admin.admins.store'), $this->fakeData());

        $response->assertSuccessful();

        $this->assertEquals(++$count, Admin::count());
    }

    public function test_can_edit_admin()
    {
        $this->admin->givePermissionTo('admin');

        $response = $this->put(route('admin.admins.update', Admin::latest()->first()), $this->fakeData());

        $response->assertSuccessful();
    }

    public function test_admin_can_reset_password()
    {
        $this->admin->givePermissionTo('admin');

        $this->expectsJobs(SendSMS::class);

        $response = $this->post(route('admin.admins.reset-password'), [
            'admin_id' => $this->admin->id,
        ]);

        $response->assertSuccessful();
    }
}
