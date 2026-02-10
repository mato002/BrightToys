<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MemberWallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'John Doe', 'email' => 'member1@brighttoys.com', 'phone' => '+254700000001'],
            ['name' => 'Jane Achieng', 'email' => 'member2@brighttoys.com', 'phone' => '+254700000002'],
            ['name' => 'Peter Otieno', 'email' => 'member3@brighttoys.com', 'phone' => '+254700000003'],
            ['name' => 'Mary Wanjiku', 'email' => 'member4@brighttoys.com', 'phone' => '+254700000004'],
            ['name' => 'Alex Kamau', 'email' => 'member5@brighttoys.com', 'phone' => '+254700000005'],
        ];

        foreach ($members as $data) {
            $token = Str::random(40);

            $member = Member::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'status' => 'pending',
                    'onboarding_token' => $token,
                    'onboarding_token_expires_at' => now()->addDays(14),
                ]
            );

            // Ensure wallets exist
            MemberWallet::firstOrCreate(
                ['member_id' => $member->id, 'type' => MemberWallet::TYPE_WELFARE],
                ['balance' => 0]
            );

            MemberWallet::firstOrCreate(
                ['member_id' => $member->id, 'type' => MemberWallet::TYPE_INVESTMENT],
                ['balance' => 0]
            );

            $this->command?->info("Onboarding link for {$member->name}: " . url('/onboarding/' . $member->onboarding_token));
        }
    }
}

