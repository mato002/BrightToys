<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AdminRole;
use App\Models\Partner;
use App\Models\PartnerOwnership;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin roles
        $superAdminRole = AdminRole::where('name', 'super_admin')->first();
        $financeAdminRole = AdminRole::where('name', 'finance_admin')->first();
        $storeAdminRole = AdminRole::where('name', 'store_admin')->first();

        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@brighttoys.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'is_partner' => false,
            ]
        );
        if ($superAdminRole && !$superAdmin->adminRoles->contains($superAdminRole->id)) {
            $superAdmin->adminRoles()->attach($superAdminRole->id);
        }

        // Create Finance Admin
        $financeAdmin = User::firstOrCreate(
            ['email' => 'finance@brighttoys.com'],
            [
                'name' => 'Finance Administrator',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'is_partner' => false,
            ]
        );
        if ($financeAdminRole && !$financeAdmin->adminRoles->contains($financeAdminRole->id)) {
            $financeAdmin->adminRoles()->attach($financeAdminRole->id);
        }

        // Create Store Admin
        $storeAdmin = User::firstOrCreate(
            ['email' => 'store@brighttoys.com'],
            [
                'name' => 'Store Administrator',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'is_partner' => false,
            ]
        );
        if ($storeAdminRole && !$storeAdmin->adminRoles->contains($storeAdminRole->id)) {
            $storeAdmin->adminRoles()->attach($storeAdminRole->id);
        }

        // Create Finance + Store Admin (multiple roles)
        $multiRoleAdmin = User::firstOrCreate(
            ['email' => 'multirole@brighttoys.com'],
            [
                'name' => 'Multi Role Admin',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'is_partner' => false,
            ]
        );
        if ($financeAdminRole && !$multiRoleAdmin->adminRoles->contains($financeAdminRole->id)) {
            $multiRoleAdmin->adminRoles()->attach($financeAdminRole->id);
        }
        if ($storeAdminRole && !$multiRoleAdmin->adminRoles->contains($storeAdminRole->id)) {
            $multiRoleAdmin->adminRoles()->attach($storeAdminRole->id);
        }

        // Create Partner Users (10 partners)
        $partners = [
            ['name' => 'Partner One', 'email' => 'partner1@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Two', 'email' => 'partner2@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Three', 'email' => 'partner3@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Four', 'email' => 'partner4@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Five', 'email' => 'partner5@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Six', 'email' => 'partner6@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Seven', 'email' => 'partner7@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Eight', 'email' => 'partner8@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Nine', 'email' => 'partner9@brighttoys.com', 'percentage' => 10.00],
            ['name' => 'Partner Ten', 'email' => 'partner10@brighttoys.com', 'percentage' => 10.00],
        ];

        foreach ($partners as $index => $partnerData) {
            $user = User::firstOrCreate(
                ['email' => $partnerData['email']],
                [
                    'name' => $partnerData['name'],
                    'password' => Hash::make('password123'),
                    'is_admin' => false,
                    'is_partner' => true,
                ]
            );

            $partner = Partner::firstOrCreate(
                ['email' => $partnerData['email']],
                [
                    'user_id' => $user->id,
                    'name' => $partnerData['name'],
                    'email' => $partnerData['email'],
                    'status' => 'active',
                ]
            );

            // Create ownership record
            PartnerOwnership::firstOrCreate(
                [
                    'partner_id' => $partner->id,
                    'effective_from' => now()->subYear(),
                ],
                [
                    'percentage' => $partnerData['percentage'],
                ]
            );
        }

        $this->command->info('Test users created successfully!');
        $this->command->info('Super Admin: superadmin@brighttoys.com / password123');
        $this->command->info('Finance Admin: finance@brighttoys.com / password123');
        $this->command->info('Store Admin: store@brighttoys.com / password123');
        $this->command->info('Multi Role Admin: multirole@brighttoys.com / password123');
        $this->command->info('Partners: partner1@brighttoys.com to partner10@brighttoys.com / password123');
    }
}
