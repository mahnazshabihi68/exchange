<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Array of admins.
         */

        $users = [
            [
                'first_name' => 'علی',
                'last_name' => 'خدمتی',
                'email' => 'ali@khedmati.ir',
                'mobile' => '09122958172',
                'password' => bcrypt('ali@Ali199712'),
            ],
            [
                'first_name' => 'کاربر',
                'last_name' => 'کل',
                'email' => 'test@example.com',
                'mobile' => '09121234567',
                'password' => bcrypt('09121234567@Admin'),
            ]
        ];

        /**
         * Create admin and assign role.
         */

        foreach ($users as $key => $user) {

            if ($key === 0) {

                User::create($user);

            }

            $user = Admin::create($user);

            $user->assignRole(Role::where('guard_name', 'admin')->first());

        }
    }
}
