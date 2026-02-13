<?php

/**
 * Example Usage of Accounting Rules Service
 * 
 * This file demonstrates how to use the AccountingRuleService
 * to automatically create journal entries when transactions occur.
 */

use App\Services\AccountingRuleService;
use Illuminate\Support\Facades\DB;

// ============================================
// Example 1: Salary Advance
// ============================================
function processSalaryAdvance($employeeId, $amount)
{
    DB::beginTransaction();
    try {
        // Your business logic here
        // e.g., update employee record, send notification, etc.
        
        // Apply accounting rule automatically
        $accountingService = app(AccountingRuleService::class);
        $journalEntry = $accountingService->applyRule(
            triggerEvent: 'salary_advance',
            amount: $amount,
            metadata: [
                'reference_number' => "SA-{$employeeId}-" . now()->format('Ymd'),
                'transaction_details' => "Salary advance for Employee #{$employeeId}",
                'transaction_date' => now()->toDateString(),
                'description' => "Monthly salary advance payment",
                'branch_name' => 'Corporate (HQ)',
                'auto_post' => true,
            ]
        );
        
        DB::commit();
        return $journalEntry;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

// ============================================
// Example 2: Loan Disbursement
// ============================================
function disburseLoan($loanId, $amount)
{
    DB::beginTransaction();
    try {
        // Update loan status
        $loan = \App\Models\Loan::findOrFail($loanId);
        $loan->update(['status' => 'disbursed', 'disbursed_at' => now()]);
        
        // Apply accounting rule
        $accountingService = app(AccountingRuleService::class);
        $journalEntry = $accountingService->applyRule(
            triggerEvent: 'loan_ledger',
            amount: $amount,
            metadata: [
                'reference_number' => "LOAN-{$loanId}",
                'transaction_details' => "Loan disbursement for Loan #{$loanId}",
                'transaction_date' => now()->toDateString(),
                'description' => "Loan principal disbursement",
            ]
        );
        
        DB::commit();
        return $journalEntry;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

// ============================================
// Example 3: Loan Interest Accrual
// ============================================
function accrueLoanInterest($loanId, $interestAmount)
{
    DB::beginTransaction();
    try {
        // Update loan interest record
        // Your business logic here
        
        // Apply accounting rule
        $accountingService = app(AccountingRuleService::class);
        $journalEntry = $accountingService->applyRule(
            triggerEvent: 'loan_interest',
            amount: $interestAmount,
            metadata: [
                'reference_number' => "INT-{$loanId}-" . now()->format('Ymd'),
                'transaction_details' => "Interest accrual for Loan #{$loanId}",
                'transaction_date' => now()->toDateString(),
            ]
        );
        
        DB::commit();
        return $journalEntry;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

// ============================================
// Example 4: Monthly Contribution
// ============================================
function recordContribution($memberId, $amount)
{
    DB::beginTransaction();
    try {
        // Record contribution in your system
        // Your business logic here
        
        // Apply accounting rule
        $accountingService = app(AccountingRuleService::class);
        $journalEntry = $accountingService->applyRule(
            triggerEvent: 'contribution',
            amount: $amount,
            metadata: [
                'reference_number' => "CONT-{$memberId}-" . now()->format('Ym'),
                'transaction_details' => "Monthly contribution from Member #{$memberId}",
                'transaction_date' => now()->toDateString(),
            ]
        );
        
        DB::commit();
        return $journalEntry;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

// ============================================
// Example 5: Check if Rule Exists Before Applying
// ============================================
function safeApplyRule($triggerEvent, $amount, $metadata = [])
{
    $accountingService = app(AccountingRuleService::class);
    
    // Check if rule exists
    if (!$accountingService->hasRule($triggerEvent)) {
        throw new \Exception("No active accounting rule found for trigger: {$triggerEvent}");
    }
    
    // Apply the rule
    return $accountingService->applyRule($triggerEvent, $amount, $metadata);
}

// ============================================
// Example 6: Create Draft Entry (Not Auto-Posted)
// ============================================
function createDraftJournalEntry($triggerEvent, $amount, $metadata = [])
{
    $accountingService = app(AccountingRuleService::class);
    
    // Set auto_post to false to create a draft entry
    $metadata['auto_post'] = false;
    
    return $accountingService->applyRule($triggerEvent, $amount, $metadata);
}

// ============================================
// Example 7: Using in a Controller
// ============================================
class LoanController extends \App\Http\Controllers\Controller
{
    public function disburse(Request $request, $loanId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);
        
        try {
            $journalEntry = disburseLoan($loanId, $request->amount);
            
            return redirect()->route('loans.show', $loanId)
                ->with('success', 'Loan disbursed and journal entry created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
