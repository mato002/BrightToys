<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        // Additional customers
        User::factory(10)->create();

        // Admin roles must be seeded first
        $this->call([
            AdminRoleSeeder::class,
        ]);

        // Test users for partnership system
        $this->call([
            TestUsersSeeder::class,
        ]);

        // Projects
        $this->call([
            ProjectSeeder::class,
        ]);

        // Catalog data
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);

        // Sample members for onboarding demo
        $this->call([
            MemberSeeder::class,
        ]);

        // Penalty rates for payment plans
        $this->call([
            PenaltyRateSeeder::class,
        ]);

        // Accounting data for testing
        $this->call([
            AccountingSeeder::class,
        ]);
    }
}
