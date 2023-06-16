<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\Admin;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\AdminTestCase;

class RolePermissionTest extends AdminTestCase
{
    public function test_can_create_role()
    {
        $this->admin->givePermissionTo('role');

        $response = $this->post(route('admin.roles.store'), [
            'name' => 'some-role',
            'permissions' => Permission::pluck('id')->toArray(),
        ]);

        $response->assertSuccessful();

        $this->assertNotNull(Role::whereName('some-role')->first());
    }

    public function test_should_not_be_able_to_add_role_without_permission()
    {
        $response = $this->post(route('admin.roles.store'), [
            'name' => 'some-role',
            'permissions' => Permission::pluck('id')->toArray(),
        ]);

        $response->assertForbidden();

        $this->assertNull(Role::whereName('some-role')->first());
    }

    public function test_can_see_admin_role_permissions()
    {
        $this->admin->givePermissionTo('role');

        $response = $this->get(route('admin.roles.query', 'admin'));

        $response->assertSuccessful()->assertJsonStructure([
            'roles', 'permissions',
        ]);
    }

    public function test_can_see_user_role_permissions()
    {
        $this->admin->givePermissionTo('role');

        $response = $this->get(route('admin.roles.query', 'user'));

        $response->assertSuccessful()->assertJsonStructure([
            'roles', 'permissions',
        ]);
    }

    public function test_can_edit_role()
    {
        $this->admin->givePermissionTo('role');

        $role = Role::create([
            'name' => 'some-role',
            'guard_name' => 'admin',
        ]);

        $response = $this->put(route('admin.roles.update', $role), [
            'name' => 'one-role',
            'permissions' => ['role'],
        ]);

        $response->assertSuccessful();
    }

    public function test_should_not_be_able_to_edit_role()
    {
        $role = Role::create([
            'name' => 'some-role',
            'guard_name' => 'admin',
        ]);

        $response = $this->put(route('admin.roles.update', $role), [
            'name' => 'one-role',
            'permissions' => ['role'],
        ]);

        $response->assertForbidden();
    }

    public function test_can_delete_role()
    {
        $this->admin->givePermissionTo('role');

        $response = $this->delete(route('admin.roles.destroy', $role = Role::skip(1)->first()));

        $response->assertSuccessful();

        $this->assertNull($role->fresh());
    }
}
