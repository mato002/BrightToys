<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AdminRole;
use App\Models\Partner;
use App\Models\PartnerOwnership;
use App\Models\PartnerContribution;
use App\Models\FinancialRecord;
use App\Models\Project;
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
        $chairmanRole = AdminRole::where('name', 'chairman')->first();

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

        // Create Chairman (Board Chairperson, also a Partner)
        $chairman = User::firstOrCreate(
            ['email' => 'chairman@brighttoys.com'],
            [
                'name' => 'Board Chairman',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'is_partner' => true,
            ]
        );
        if ($chairmanRole && !$chairman->adminRoles->contains($chairmanRole->id)) {
            $chairman->adminRoles()->attach($chairmanRole->id);
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
            
            // Ensure is_partner flag is set
            if (!$user->is_partner) {
                $user->update(['is_partner' => true]);
            }

            $partner = Partner::firstOrCreate(
                ['email' => $partnerData['email']],
                [
                    'user_id' => $user->id,
                    'name' => $partnerData['name'],
                    'email' => $partnerData['email'],
                    'status' => 'active',
                ]
            );
            
            // Always ensure user_id is set correctly (in case partner existed before user)
            if ($partner->user_id !== $user->id) {
                $partner->update(['user_id' => $user->id]);
            }

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

        // Chairman's Partner profile and ownership so his pages are populated
        $chairmanPartner = Partner::firstOrCreate(
            ['email' => 'chairman@brighttoys.com'],
            [
                'user_id' => $chairman->id,
                'name' => 'Board Chairman',
                'phone' => '+254700000000',
                'status' => 'active',
                'notes' => 'Founding chairman and lead investor in BrightToys.',
            ]
        );

        if ($chairmanPartner->user_id !== $chairman->id) {
            $chairmanPartner->update(['user_id' => $chairman->id]);
        }

        // Chairman ownership (e.g. 40%)
        PartnerOwnership::firstOrCreate(
            [
                'partner_id' => $chairmanPartner->id,
                'effective_from' => now()->subYears(2),
            ],
            [
                'percentage' => 40.00,
            ]
        );

        // Seed some financial/contribution data so chairman dashboards have data
        $project = Project::where('slug', 'brighttoys-ecommerce')->first();

        // Initial investment contribution from chairman
        $initialContributionDate = now()->subMonths(6);
        PartnerContribution::firstOrCreate(
            [
                'partner_id' => $chairmanPartner->id,
                'type' => 'contribution',
                'amount' => 50000.00,
                'contributed_at' => $initialContributionDate,
            ],
            [
                'fund_type' => 'investment',
                'currency' => 'USD',
                'reference' => 'CH-INITIAL-INVEST',
                'notes' => 'Initial capital injection by the chairman.',
                'status' => 'approved',
                'created_by' => $chairman->id,
                'approved_by' => $superAdmin->id ?? $chairman->id,
                'approved_at' => $initialContributionDate->copy()->addDay(),
                'is_archived' => false,
            ]
        );

        // Example profit distribution / withdrawal
        $payoutDate = now()->subMonths(3);
        PartnerContribution::firstOrCreate(
            [
                'partner_id' => $chairmanPartner->id,
                'type' => 'profit_distribution',
                'amount' => 8000.00,
                'contributed_at' => $payoutDate,
            ],
            [
                'fund_type' => 'investment',
                'currency' => 'USD',
                'reference' => 'CH-PROFIT-DIST-001',
                'notes' => 'First profit distribution to the chairman.',
                'status' => 'approved',
                'created_by' => $chairman->id,
                'approved_by' => $superAdmin->id ?? $chairman->id,
                'approved_at' => $payoutDate->copy()->addDay(),
                'is_archived' => false,
            ]
        );

        // Financial record linking chairman funds to the main BrightToys e-commerce project
        $expenseDate = now()->subMonths(5);
        FinancialRecord::firstOrCreate(
            [
                'type' => 'expense',
                'fund_type' => 'investment',
                'amount' => 15000.00,
                'occurred_at' => $expenseDate,
                'partner_id' => $chairmanPartner->id,
            ],
            [
                'category' => 'Platform development',
                'currency' => 'USD',
                'paid_from' => 'Chairman Investment Pool',
                'description' => 'Platform development and launch costs funded by the chairman.',
                'order_id' => null,
                'project_id' => $project?->id,
                'status' => 'approved',
                'created_by' => $financeAdmin->id ?? $superAdmin->id ?? $chairman->id,
                'approved_by' => $superAdmin->id ?? null,
                'approved_at' => $expenseDate->copy()->addDays(2),
                'is_archived' => false,
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->info('Super Admin: superadmin@brighttoys.com / password123');
        $this->command->info('Finance Admin: finance@brighttoys.com / password123');
        $this->command->info('Store Admin: store@brighttoys.com / password123');
        $this->command->info('Multi Role Admin: multirole@brighttoys.com / password123');
        $this->command->info('Chairman: chairman@brighttoys.com / password123');
        $this->command->info('Partners: partner1@brighttoys.com to partner10@brighttoys.com / password123');
    }
}
