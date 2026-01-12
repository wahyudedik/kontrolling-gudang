<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Supervisor 1
        User::create([
            'name' => 'Supervisor 1',
            'email' => 'supervisor1@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);

        // Supervisor 2
        User::create([
            'name' => 'Supervisor 2',
            'email' => 'supervisor2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);
    }
}
