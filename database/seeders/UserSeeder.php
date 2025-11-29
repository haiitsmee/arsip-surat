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
        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@arsip.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Staff User
        User::create([
            'name' => 'Staff Arsip',
            'email' => 'staff@arsip.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Demo Users
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@arsip.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@arsip.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);
    }
}