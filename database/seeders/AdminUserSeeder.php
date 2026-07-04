<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@monks.local'],
            [
                'name'     => 'ຜູ້ດູແລລະບົບ',
                'password' => Hash::make('admin1234'),
                'role'     => 'admin',
            ]
        );
    }
}
