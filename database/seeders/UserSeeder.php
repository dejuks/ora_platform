<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN USER — Super Admin, bypasses every module/role check
        User::create([
            'employee_no' => 'EMP-0001',
            'first_name' => 'Admin',
            'middle_name' => null,
            'last_name' => 'User',

            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '0911111111',

            'gender' => 'Male',
            'date_of_birth' => '1990-01-01',

            'profile_photo' => null,

            'password' => Hash::make('password'),

            'status' => 'Active',
            'is_super_admin' => true,
            'email_verified' => true,
            'email_verified_at' => now(),

            'last_login_at' => null,
            'last_login_ip' => null,

            'failed_login_attempts' => 0,
            'locked_until' => null,

            'created_by' => null,
            'updated_by' => null,
        ]);

        // NORMAL USER — gets the Journal "Author" role via DatabaseSeeder
        User::create([
            'employee_no' => 'EMP-0002',
            'first_name' => 'John',
            'middle_name' => null,
            'last_name' => 'Doe',

            'username' => 'johndoe',
            'email' => 'user@example.com',
            'phone' => '0922222222',

            'gender' => 'Male',
            'date_of_birth' => '1995-05-10',

            'profile_photo' => null,

            'password' => Hash::make('password'),

            'status' => 'Active',
            'email_verified' => true,
            'email_verified_at' => now(),

            'last_login_at' => null,
            'last_login_ip' => null,

            'failed_login_attempts' => 0,
            'locked_until' => null,

            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}