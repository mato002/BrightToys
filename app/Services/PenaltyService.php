<?php

namespace App\Services;

use App\Models\MemberWallet;
use App\Models\Partner;
use App\Models\PenaltyAdjustment;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class PenaltyService
{
    /**
     * Apply the effect of an approved penalty adjustment (wallet + logs).
     */
    public static function applyAdjustment(PenaltyAdjustment $adjustment): void
    {
        if ($adjustment->status !== 'approved') {
            return;
        }

        /** @var Partner $partner */
        $partner = $adjustment->partner;
        if (! $partner) {
            return;
        }

        // Pauses do not affect wallet balances directly – handled in calculations.
        if ($adjustment->type === 'pause') {
            return;
        }

        $amount = $adjustment->amount ?? 0;
        if ($amount <= 0) {
            return;
        }

        // For manual penalties/waivers we post against the investment wallet by default.
        $wallet = MemberWallet::firstOrCreate(
            [
                'partner_id' => $partner->id,
                'type' => MemberWallet::TYPE_INVESTMENT,
            ],
            [
                'balance' => 0,
            ]
        );

        $balance = $wallet->balance ?? 0;
        $direction = $adjustment->type === 'apply' ? 'debit' : 'credit';

        if ($direction === 'debit') {
            $balance -= $amount;
        } else {
            $balance += $amount;
        }

        $wallet->balance = $balance;
        $wallet->save();

        WalletTransaction::create([
            'member_wallet_id' => $wallet->id,
            'partner_id' => $partner->id,
            'fund_type' => $wallet->type,
            'direction' => $direction,
            'type' => $adjustment->type === 'apply' ? 'penalty' : 'penalty_waiver',
            'amount' => $amount,
            'balance_after' => $balance,
            'occurred_at' => now(),
            'reference' => 'Penalty ' . $adjustment->type . ' #' . $adjustment->id,
            'description' => $adjustment->reason,
            'source_type' => PenaltyAdjustment::class,
            'source_id' => $adjustment->id,
            'created_by' => Auth::id() ?? $adjustment->created_by,
        ]);
    }

    /**
     * Check if penalties should be paused for a given partner and month.
     */
    public static function isPausedForMonth(Partner $partner, int $year, int $month): bool
    {
        $startOfMonth = now()->setDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        return PenaltyAdjustment::approved()
            ->where('partner_id', $partner->id)
            ->where('type', 'pause')
            ->where('scope', 'monthly_contribution')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where(function ($q2) use ($startOfMonth, $endOfMonth) {
                    $q2->where('paused_from', '<=', $endOfMonth)
                        ->where(function ($q3) use ($startOfMonth) {
                            $q3->whereNull('paused_to')
                               ->orWhere('paused_to', '>=', $startOfMonth);
                        });
                });
            })
            ->exists();
    }
}

