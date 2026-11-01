<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gudang.test'],
            [
                'name' => 'Admin Gudang',
                'password' => 'Gudang123!',
            ]
        );
    }
}
