<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultRoleAccountsSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@orbit.local'],
            ['name' => 'Admin', 'password' => 'admin123'],
        )->forceFill(['role' => 'admin'])->save();

        User::firstOrCreate(
            ['email' => 'kasir@orbit.local'],
            ['name' => 'Kasir', 'password' => 'admin123'],
        )->forceFill(['role' => 'cashier'])->save();
    }
}
