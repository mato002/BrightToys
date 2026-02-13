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
     * Get or create wallets for a partner if they don't exist.
     */
    public static function ensureWalletsExist(Partner $partner): void
    {
        // Ensure investment wallet exists
        MemberWallet::firstOrCreate(
            [
                'partner_id' => $partner->id,
                'type' => MemberWallet::TYPE_INVESTMENT,
            ],
            [
                'balance' => 0,
            ]
        );

        // Ensure welfare wallet exists
        MemberWallet::firstOrCreate(
            [
                'partner_id' => $partner->id,
                'type' => MemberWallet::TYPE_WELFARE,
            ],
            [
                'balance' => 0,
            ]
        );
    }

    /**
     * Sync wallet balances from approved contributions (useful for fixing out-of-sync wallets).
     */
    public static function syncWalletsFromContributions(Partner $partner): void
    {
        // Ensure wallets exist
        self::ensureWalletsExist($partner);

        // Get all approved contributions
        $approvedContributions = PartnerContribution::where('partner_id', $partner->id)
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->get();

        // Calculate investment balance (case-insensitive)
        $investmentContributions = $approvedContributions
            ->filter(function($contribution) {
                return strtolower($contribution->fund_type ?? 'investment') === 'investment' 
                    && $contribution->type === 'contribution';
            })
            ->sum('amount');
        $investmentWithdrawals = $approvedContributions
            ->filter(function($contribution) {
                return strtolower($contribution->fund_type ?? 'investment') === 'investment' 
                    && in_array($contribution->type, ['withdrawal', 'profit_distribution']);
            })
            ->sum('amount');
        $investmentBalance = $investmentContributions - $investmentWithdrawals;

        // Calculate welfare balance (case-insensitive)
        $welfareContributions = $approvedContributions
            ->filter(function($contribution) {
                return strtolower($contribution->fund_type ?? 'investment') === 'welfare' 
                    && $contribution->type === 'contribution';
            })
            ->sum('amount');
        $welfareWithdrawals = $approvedContributions
            ->filter(function($contribution) {
                return strtolower($contribution->fund_type ?? 'investment') === 'welfare' 
                    && in_array($contribution->type, ['withdrawal', 'profit_distribution']);
            })
            ->sum('amount');
        $welfareBalance = $welfareContributions - $welfareWithdrawals;

        // Update wallets
        $investmentWallet = MemberWallet::where('partner_id', $partner->id)
            ->where('type', MemberWallet::TYPE_INVESTMENT)
            ->first();
        if ($investmentWallet) {
            $investmentWallet->balance = $investmentBalance;
            $investmentWallet->save();
        }

        $welfareWallet = MemberWallet::where('partner_id', $partner->id)
            ->where('type', MemberWallet::TYPE_WELFARE)
            ->first();
        if ($welfareWallet) {
            $welfareWallet->balance = $welfareBalance;
            $welfareWallet->save();
        }
    }

    /**
     * Get wallet balance, calculating from contributions if wallet doesn't exist or is out of sync.
     */
    public static function getWalletBalance(Partner $partner, string $fundType): float
    {
        // Normalize fundType
        $fundType = strtolower(trim($fundType));
        
        // Always calculate from contributions to ensure accuracy
        // This is the source of truth - wallets are just for performance/caching
        $calculatedBalance = self::calculateBalanceFromContributions($partner, $fundType);
        
        // Ensure wallets exist and sync them
        self::ensureWalletsExist($partner);
        self::syncWalletsFromContributions($partner);
        
        // Return the calculated balance (source of truth)
        return $calculatedBalance;
    }

    /**
     * Calculate balance from contributions directly.
     */
    private static function calculateBalanceFromContributions(Partner $partner, string $fundType): float
    {
        // Normalize fundType to lowercase for comparison
        $fundType = strtolower($fundType);
        
        $approvedContributions = PartnerContribution::where('partner_id', $partner->id)
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->get();

        // Calculate contributions (additions to balance)
        // Normalize fund_type for comparison (case-insensitive)
        $contributions = $approvedContributions
            ->filter(function($contribution) use ($fundType) {
                return strtolower($contribution->fund_type ?? 'investment') === $fundType 
                    && $contribution->type === 'contribution';
            })
            ->sum('amount');
        
        // Calculate withdrawals and profit distributions (subtractions from balance)
        $withdrawals = $approvedContributions
            ->filter(function($contribution) use ($fundType) {
                return strtolower($contribution->fund_type ?? 'investment') === $fundType 
                    && in_array($contribution->type, ['withdrawal', 'profit_distribution']);
            })
            ->sum('amount');

        return $contributions - $withdrawals;
    }

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

