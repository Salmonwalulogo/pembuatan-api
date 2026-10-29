<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DefaultRoleAccountsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_admin_and_cashier_accounts(): void
    {
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DefaultRoleAccountsSeeder']);

        $this->assertDatabaseHas('users', [
            'name' => 'Admin',
            'email' => 'admin@orbit.local',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Kasir',
            'email' => 'kasir@orbit.local',
            'role' => 'cashier',
        ]);

        $this->assertTrue(Hash::check(
            'admin123',
            User::where('email', 'admin@orbit.local')->value('password'),
        ));
        $this->assertTrue(Hash::check(
            'admin123',
            User::where('email', 'kasir@orbit.local')->value('password'),
        ));
    }
}
