<?php

namespace App\Services;

use App\Models\AccountingRule;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service for automatically applying accounting rules when transactions occur.
 * 
 * This service follows double-entry bookkeeping principles:
 * - Every transaction must have equal debits and credits
 * - Rules define automatic journal entries based on trigger events
 * - Ensures consistency and reduces manual entry errors
 */
class AccountingRuleService
{
    /**
     * Apply accounting rules for a given trigger event
     * 
     * @param string $triggerEvent The event that triggers the rule (e.g., 'salary_advance', 'loan_ledger')
     * @param float $amount The transaction amount
     * @param array $metadata Additional data for the journal entry (reference_number, description, etc.)
     * @return JournalEntry|null The created journal entry or null if no rule found
     * @throws \Exception If rule application fails
     */
    public function applyRule(string $triggerEvent, float $amount, array $metadata = []): ?JournalEntry
    {
        // Find active rule for this trigger event
        $rule = AccountingRule::where('trigger_event', $triggerEvent)
            ->where('is_active', true)
            ->first();

        if (!$rule) {
            Log::warning("No active accounting rule found for trigger event: {$triggerEvent}");
            return null;
        }

        // Validate amount
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Transaction amount must be greater than zero.");
        }

        DB::beginTransaction();
        try {
            // Create journal entry
            $journalEntry = JournalEntry::create([
                'transaction_id' => JournalEntry::generateTransactionId(),
                'transaction_date' => $metadata['transaction_date'] ?? now()->toDateString(),
                'reference_number' => $metadata['reference_number'] ?? null,
                'transaction_details' => $metadata['transaction_details'] ?? $rule->description ?? "Auto-generated from rule: {$rule->name}",
                'comments' => $metadata['comments'] ?? "Applied rule: {$rule->name}",
                'branch_name' => $metadata['branch_name'] ?? 'Corporate (HQ)',
                'status' => $metadata['auto_post'] ?? true ? 'posted' : 'draft',
                'posted_by' => $metadata['posted_by'] ?? auth()->id(),
                'posted_at' => $metadata['auto_post'] ?? true ? now() : null,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Create debit line
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $rule->debit_account_id,
                'entry_type' => 'debit',
                'amount' => $amount,
                'description' => $metadata['description'] ?? "Debit entry for {$rule->name}",
            ]);

            // Create credit line
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $rule->credit_account_id,
                'entry_type' => 'credit',
                'amount' => $amount,
                'description' => $metadata['description'] ?? "Credit entry for {$rule->name}",
            ]);

            DB::commit();

            Log::info("Accounting rule applied successfully", [
                'rule_id' => $rule->id,
                'rule_name' => $rule->name,
                'trigger_event' => $triggerEvent,
                'amount' => $amount,
                'journal_entry_id' => $journalEntry->id,
            ]);

            return $journalEntry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to apply accounting rule", [
                'rule_id' => $rule->id,
                'trigger_event' => $triggerEvent,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check if a rule exists for a given trigger event
     * 
     * @param string $triggerEvent
     * @return bool
     */
    public function hasRule(string $triggerEvent): bool
    {
        return AccountingRule::where('trigger_event', $triggerEvent)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get all active rules grouped by trigger event
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getActiveRules()
    {
        return AccountingRule::with(['debitAccount', 'creditAccount'])
            ->where('is_active', true)
            ->orderBy('trigger_event')
            ->get()
            ->groupBy('trigger_event');
    }

    /**
     * Post a partner contribution to the accounting system
     * Creates a journal entry for the contribution/withdrawal/profit distribution
     * 
     * @param \App\Models\PartnerContribution $contribution
     * @return JournalEntry|null
     */
    public function postContribution(\App\Models\PartnerContribution $contribution): ?JournalEntry
    {
        if ($contribution->status !== 'approved') {
            return null;
        }

        // Check if journal entry already exists for this contribution
        $existingEntry = JournalEntry::where('reference_number', "CONT-{$contribution->id}")
            ->orWhere('reference_number', $contribution->reference)
            ->where('comments', 'like', "%contribution #{$contribution->id}%")
            ->first();

        if ($existingEntry) {
            Log::info("Journal entry already exists for contribution", [
                'contribution_id' => $contribution->id,
                'journal_entry_id' => $existingEntry->id,
            ]);
            return $existingEntry;
        }

        // Get accounts from wallet mappings or use defaults
        $cashAccount = \App\Models\ChartOfAccount::where('code', '1000')->first(); // Cash and Cash Equivalents
        $equityAccount = \App\Models\ChartOfAccount::where('code', '3000')->first(); // Partner Equity

        if (!$cashAccount || !$equityAccount) {
            Log::warning("Required accounts not found for contribution posting", [
                'contribution_id' => $contribution->id,
            ]);
            return null;
        }

        $amount = $contribution->amount;
        $fundType = $contribution->fund_type ?: 'investment';
        $type = $contribution->type;

        // Determine debit and credit accounts based on contribution type
        $debitAccount = null;
        $creditAccount = null;
        $description = '';

        if ($type === 'contribution') {
            // Contribution: Debit Cash, Credit Equity
            $debitAccount = $cashAccount;
            $creditAccount = $equityAccount;
            $partnerName = $contribution->partner ? $contribution->partner->name : 'Partner';
            $description = "Partner {$fundType} contribution from {$partnerName}";
        } elseif (in_array($type, ['withdrawal', 'profit_distribution'])) {
            // Withdrawal/Profit Distribution: Debit Equity, Credit Cash
            $debitAccount = $equityAccount;
            $creditAccount = $cashAccount;
            $typeLabel = $type === 'withdrawal' ? 'withdrawal' : 'profit distribution';
            $partnerName = $contribution->partner ? $contribution->partner->name : 'Partner';
            $description = "Partner {$fundType} {$typeLabel} to {$partnerName}";
        } else {
            // Unknown type, skip
            return null;
        }

        DB::beginTransaction();
        try {
            // Create journal entry
            $userId = $contribution->approved_by ?? $contribution->created_by ?? auth()->id() ?? 1;
            
            $journalEntry = JournalEntry::create([
                'transaction_id' => JournalEntry::generateTransactionId(),
                'transaction_date' => $contribution->contributed_at ?? now(),
                'reference_number' => $contribution->reference ?? "CONT-{$contribution->id}",
                'transaction_details' => $description,
                'comments' => "Auto-posted from partner contribution #{$contribution->id}",
                'branch_name' => 'Corporate (HQ)',
                'status' => 'posted',
                'posted_by' => $userId,
                'posted_at' => $contribution->approved_at ?? now(),
                'created_by' => $userId,
            ]);

            // Create debit line
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $debitAccount->id,
                'entry_type' => 'debit',
                'amount' => $amount,
                'description' => $description,
            ]);

            // Create credit line
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $creditAccount->id,
                'entry_type' => 'credit',
                'amount' => $amount,
                'description' => $description,
            ]);

            DB::commit();

            Log::info("Contribution posted to accounting system", [
                'contribution_id' => $contribution->id,
                'journal_entry_id' => $journalEntry->id,
                'amount' => $amount,
                'type' => $type,
            ]);

            return $journalEntry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to post contribution to accounting system", [
                'contribution_id' => $contribution->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
