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
        \App\Models\User::create([
            'name' => 'Admin Cleanup',
            'email' => 'admin@cleanup.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'Employee Cleanup',
            'email' => 'employee@cleanup.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        \App\Models\User::create([
            'name' => 'Customer Cleanup',
            'email' => 'customer@cleanup.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);
    }
}
