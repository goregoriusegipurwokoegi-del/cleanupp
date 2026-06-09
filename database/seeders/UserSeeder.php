<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'Cucisepatu.cp99@gmail.com'],
            [
                'name' => 'Admin Cleanup',
                'password' => bcrypt('@CuciRsp2026'),
                'password_plain' => '@CuciRsp2026',
                'role' => 'admin',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'employee@cleanup.com'],
            [
                'name' => 'Employee Cleanup',
                'password' => bcrypt('password'),
                'role' => 'employee',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'customer@cleanup.com'],
            [
                'name' => 'Customer Cleanup',
                'password' => bcrypt('password'),
                'role' => 'customer',
            ]
        );
    }
}
