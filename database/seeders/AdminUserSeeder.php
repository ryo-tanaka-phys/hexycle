<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hexycle.local'],
            [
                'name' => 'Hexycle Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}