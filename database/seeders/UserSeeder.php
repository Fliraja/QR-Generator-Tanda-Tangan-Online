<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'nip' => '198501012010011001',
                'name' => 'Admin TU',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '199002022015022002',
                'name' => 'Staf TU 1',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '199303032018033003',
                'name' => 'Staf TU 2',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
