<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Lyseng',
            'email' => 'lyseng@mail.com',
            'password' => bcrypt('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Uy Lyseng',
            'email' => 'lysenguy@mail.com',
            'password' => bcrypt('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
