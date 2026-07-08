<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nip' => '198501012010011001',
            'name' => 'Admin TU',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
