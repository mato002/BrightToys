<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Administrator'],
            ['name' => 'finance_admin', 'display_name' => 'Finance Administrator'],
            ['name' => 'store_admin', 'display_name' => 'Store Administrator'],
            ['name' => 'chairman', 'display_name' => 'Chairman'],
            ['name' => 'treasurer', 'display_name' => 'Treasurer'],
        ];

        foreach ($roles as $role) {
            AdminRole::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
