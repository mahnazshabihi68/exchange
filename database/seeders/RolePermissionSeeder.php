<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        /**
         * Array of permissions.
         */

        $permissionsArray = [
            'admin' => [
                'seo-setting',
                'appearance-setting',
                'social-setting',
                'api-setting',
                'financial-setting',
                'notification',
                'wallet-address',
                'admin',
                'role',
                'user',
                'document',
                'ticket',
                'transaction',
                'withdraw',
                'deposit',
                'accountancy',
                'exchange',
                'maintenance-mode',
                'cache-clear',
                'database-backup',
                'sms',
                'group',
                'privacy-policies',
                'symbol',
                'blockchain',
                'market'
            ],
            'user' => [
                'deposit',
                'withdraw',
                'trade',
            ],
        ];

        /**
         * Create permissions.
         */

        foreach ($permissionsArray as $guard => $permissions) {

            foreach ($permissions as $permission) {

                Permission::create([
                    'guard_name' => $guard,
                    'name' => $permission
                ]);

            }

        }

        /**
         * Array of roles.
         */

        $rolesArray = [
            'admin' => [
                'مدیر کل',
                'مدیر حسابداری',
                'مدیر ارتباط با مشتریان'
            ],
            'user' => [
                'تریدر',
                'استراتژیست'
            ]
        ];

        /**
         * Create super admin role.
         */

        foreach ($rolesArray as $guard => $roles) {

            foreach ($roles as $role) {

                $createdRole = Role::create([
                    'guard_name' => $guard,
                    'name' => $role
                ]);

                if ($guard === 'admin') {

                    if ($role === 'مدیر کل') {

                        $createdRole->syncPermissions(Permission::where('guard_name', $guard)->get());

                    } elseif ($role === 'مدیر حسابداری') {

                        $createdRole->syncPermissions(Permission::where('guard_name', $guard)->whereIn('name', [
                            'transaction', 'deposit', 'withdraw', 'accountancy', 'user'
                        ])->get());

                    } elseif ($role === 'مدیر ارتباط با مشتریان') {

                        $createdRole->syncPermissions(Permission::where('guard_name', $guard)->whereIn('name', [
                            'user', 'notification', 'ticket', 'document'
                        ])->get());

                    }

                }

            }

        }

    }
}
