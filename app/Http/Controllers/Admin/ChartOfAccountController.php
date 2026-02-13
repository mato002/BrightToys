<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the chart of accounts.
     */
    public function index()
    {
        $accountTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];
        $accounts = ChartOfAccount::where('is_active', true)
            ->with('parent')
            ->orderBy('code')
            ->get()
            ->groupBy('type');
        
        return view('admin.accounting.chart-of-accounts.index', compact('accountTypes', 'accounts'));
    }

    /**
     * Show the form for creating a new chart of account.
     */
    public function create()
    {
        $accountTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];
        $parentAccounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get();
        
        return view('admin.accounting.chart-of-accounts.create', compact('accountTypes', 'parentAccounts'));
    }

    /**
     * Store a newly created chart of account.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:chart_of_accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:ASSET,LIABILITY,EQUITY,INCOME,EXPENSE',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? true : false;

        ChartOfAccount::create($data);

        return redirect()->route('admin.accounting.chart-of-accounts.index')
            ->with('success', 'Chart of account created successfully.');
    }

    /**
     * Display the specified chart of account.
     */
    public function show(ChartOfAccount $chartOfAccount)
    {
        $chartOfAccount->load(['parent', 'children', 'journalEntryLines.journalEntry']);
        
        return view('admin.accounting.chart-of-accounts.show', compact('chartOfAccount'));
    }

    /**
     * Show the form for editing the specified chart of account.
     */
    public function edit(ChartOfAccount $chartOfAccount)
    {
        $accountTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];
        $parentAccounts = ChartOfAccount::where('is_active', true)
            ->where('id', '!=', $chartOfAccount->id)
            ->orderBy('code')
            ->get();
        
        return view('admin.accounting.chart-of-accounts.edit', compact('chartOfAccount', 'accountTypes', 'parentAccounts'));
    }

    /**
     * Update the specified chart of account.
     */
    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:chart_of_accounts,code,' . $chartOfAccount->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:ASSET,LIABILITY,EQUITY,INCOME,EXPENSE',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:chart_of_accounts,id|different:' . $chartOfAccount->id,
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? true : false;

        $chartOfAccount->update($data);

        return redirect()->route('admin.accounting.chart-of-accounts.index')
            ->with('success', 'Chart of account updated successfully.');
    }

    /**
     * Remove the specified chart of account.
     */
    public function destroy(ChartOfAccount $chartOfAccount)
    {
        // Check if account has journal entries
        if ($chartOfAccount->journalEntryLines()->count() > 0) {
            return redirect()->route('admin.accounting.chart-of-accounts.index')
                ->with('error', 'Cannot delete account that has journal entries. Deactivate it instead.');
        }

        // Check if account has children
        if ($chartOfAccount->children()->count() > 0) {
            return redirect()->route('admin.accounting.chart-of-accounts.index')
                ->with('error', 'Cannot delete account that has child accounts. Please delete or reassign child accounts first.');
        }

        $chartOfAccount->delete();

        return redirect()->route('admin.accounting.chart-of-accounts.index')
            ->with('success', 'Chart of account deleted successfully.');
    }
}
