<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingRule;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingRuleController extends Controller
{
    /**
     * Display a listing of accounting rules
     */
    public function index()
    {
        $rules = AccountingRule::with(['debitAccount', 'creditAccount'])
            ->orderBy('name')
            ->get();
        
        return view('admin.accounting.rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new accounting rule
     */
    public function create()
    {
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get();
        
        // Common trigger events for reference
        $commonTriggers = [
            'salary_advance' => 'Salary Advances',
            'loan_ledger' => 'Loan Ledger',
            'loan_overpayment' => 'Loan Overpayments',
            'loan_interest' => 'Loan Interests',
            'contribution' => 'Monthly Contributions',
            'welfare_disbursement' => 'Welfare Disbursements',
            'investment_deposit' => 'Investment Deposits',
            'expense_payment' => 'Expense Payments',
            'revenue_collection' => 'Revenue Collection',
        ];
        
        return view('admin.accounting.rules.create', compact('accounts', 'commonTriggers'));
    }

    /**
     * Store a newly created accounting rule
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string|max:255',
            'debit_account_id' => 'required|exists:chart_of_accounts,id',
            'credit_account_id' => 'required|exists:chart_of_accounts,id|different:debit_account_id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Ensure debit and credit accounts are different
        if ($validated['debit_account_id'] == $validated['credit_account_id']) {
            return back()->withErrors(['credit_account_id' => 'Debit and Credit accounts must be different.'])
                ->withInput();
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        AccountingRule::create($validated);

        return redirect()->route('admin.accounting.chart-of-accounts.index')
            ->with('success', 'Accounting rule created successfully.');
    }

    /**
     * Show the form for editing an accounting rule
     */
    public function edit(AccountingRule $accountingRule)
    {
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get();
        
        $commonTriggers = [
            'salary_advance' => 'Salary Advances',
            'loan_ledger' => 'Loan Ledger',
            'loan_overpayment' => 'Loan Overpayments',
            'loan_interest' => 'Loan Interests',
            'contribution' => 'Monthly Contributions',
            'welfare_disbursement' => 'Welfare Disbursements',
            'investment_deposit' => 'Investment Deposits',
            'expense_payment' => 'Expense Payments',
            'revenue_collection' => 'Revenue Collection',
        ];
        
        return view('admin.accounting.rules.edit', compact('accountingRule', 'accounts', 'commonTriggers'));
    }

    /**
     * Update the specified accounting rule
     */
    public function update(Request $request, AccountingRule $accountingRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string|max:255',
            'debit_account_id' => 'required|exists:chart_of_accounts,id',
            'credit_account_id' => 'required|exists:chart_of_accounts,id|different:debit_account_id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Ensure debit and credit accounts are different
        if ($validated['debit_account_id'] == $validated['credit_account_id']) {
            return back()->withErrors(['credit_account_id' => 'Debit and Credit accounts must be different.'])
                ->withInput();
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $accountingRule->update($validated);

        return redirect()->route('admin.accounting.chart-of-accounts.index')
            ->with('success', 'Accounting rule updated successfully.');
    }

    /**
     * Remove the specified accounting rule
     */
    public function destroy(AccountingRule $accountingRule)
    {
        $accountingRule->delete();

        return redirect()->route('admin.accounting.chart-of-accounts.index')
            ->with('success', 'Accounting rule deleted successfully.');
    }
}
