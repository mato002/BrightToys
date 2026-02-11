<?php

namespace App\Services;

use App\Models\MemberWallet;
use App\Models\Partner;
use App\Models\PartnerContribution;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class WalletService
{
    /**
     * Apply an approved contribution/withdrawal/profit_distribution to member wallets.
     */
    public static function applyContribution(PartnerContribution $contribution): void
    {
        if ($contribution->status !== 'approved') {
            return;
        }

        /** @var Partner $partner */
        $partner = $contribution->partner;
        if (! $partner) {
            return;
        }

        $fundType = $contribution->fund_type ?: 'investment';

        // Only contributions and withdrawals affect wallets for now.
        $direction = null;
        if ($contribution->type === 'contribution') {
            $direction = 'credit';
        } elseif (in_array($contribution->type, ['withdrawal', 'profit_distribution'], true)) {
            $direction = 'debit';
        } else {
            return;
        }

        // Get or create partner wallet for this fund type.
        $wallet = MemberWallet::firstOrCreate(
            [
                'partner_id' => $partner->id,
                'type' => $fundType,
            ],
            [
                'balance' => 0,
            ]
        );

        $amount = $contribution->amount;
        $balance = $wallet->balance ?? 0;

        if ($direction === 'credit') {
            $balance += $amount;
        } else {
            $balance -= $amount;
        }

        $wallet->balance = $balance;
        $wallet->save();

        WalletTransaction::create([
            'member_wallet_id' => $wallet->id,
            'partner_id' => $partner->id,
            'fund_type' => $fundType,
            'direction' => $direction,
            'type' => $contribution->type,
            'amount' => $amount,
            'balance_after' => $balance,
            'occurred_at' => $contribution->contributed_at ?? now(),
            'reference' => $contribution->reference,
            'description' => $contribution->notes,
            'source_type' => PartnerContribution::class,
            'source_id' => $contribution->id,
            'created_by' => Auth::id() ?? $contribution->created_by,
        ]);
    }
}

