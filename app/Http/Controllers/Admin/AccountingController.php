<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\AccountingRule;
use App\Models\WalletAccountMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountingController extends Controller
{
    /**
     * Display the Books of Account dashboard
     */
    public function dashboard()
    {
        return view('admin.accounting.dashboard');
    }

    /**
     * High-level financial tracking dashboard (group + member view, welfare, loans, assets).
     *
     * This is the admin “Financial Overview” screen that summarises:
     * - Group-level KPIs (contributions, welfare, investments, bank/SACCO balances, loans, assets, net worth, trends)
     * - Member-level stats (totals contributed, welfare vs investment, ownership %, profit entitlement)
     * - Welfare fund utilisation and approvals (read-only overview here – detailed actions live in their respective modules)
     *
     * NOTE: The initial implementation focuses on the UI scaffold and wiring.
     *       Detailed aggregation logic can be implemented incrementally using
     *       existing accounting, contribution and loan models.
     */
    public function financialOverview()
    {
        // TODO: Replace these placeholders with real aggregates from your accounting,
        //       contributions, loans, assets and welfare tables.
        $groupSummary = [
            'total_contributions' => 0,
            'welfare_total'       => 0,
            'investment_total'    => 0,
            'net_worth'           => 0,
        ];

        $bankBalances = [];
        $outstandingLoans = [];
        $assetsSummary = [];
        $performance = [
            'monthly' => [],
            'yearly'  => [],
            'trends'  => [],
        ];

        $welfareStats = [
            'total_inflows'      => 0,
            'total_disbursements'=> 0,
            'remaining_balance'  => 0,
            'recent_disbursements' => [],
        ];

        return view('admin.accounting.financial-overview', compact(
            'groupSummary',
            'bankBalances',
            'outstandingLoans',
            'assetsSummary',
            'performance',
            'welfareStats'
        ));
    }

    /**
     * Show the form for creating a new journal entry
     */
    public function createJournal()
    {
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get();
        
        return view('admin.accounting.journal.create', compact('accounts'));
    }

    /**
     * Store a newly created journal entry
     */
    public function storeJournal(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'transaction_details' => 'nullable|string',
            'comments' => 'nullable|string',
            'debit_accounts' => 'required|array|min:1',
            'debit_accounts.*' => 'required|exists:chart_of_accounts,id',
            'debit_amounts' => 'required|array|min:1',
            'debit_amounts.*' => 'required|numeric|min:0.01',
            'credit_accounts' => 'required|array|min:1',
            'credit_accounts.*' => 'required|exists:chart_of_accounts,id',
            'credit_amounts' => 'required|array|min:1',
            'credit_amounts.*' => 'required|numeric|min:0.01',
        ]);

        // Validate that total debit equals total credit
        $totalDebit = array_sum($request->debit_amounts);
        $totalCredit = array_sum($request->credit_amounts);

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['balance' => 'Total debit amount must equal total credit amount.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::create([
                'transaction_id' => JournalEntry::generateTransactionId(),
                'transaction_date' => $request->transaction_date,
                'reference_number' => $request->reference_number,
                'transaction_details' => $request->transaction_details,
                'comments' => $request->comments,
                'branch_name' => $request->branch_name,
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
                'created_by' => auth()->id(),
            ]);

            // Create debit lines
            foreach ($request->debit_accounts as $index => $accountId) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $accountId,
                    'entry_type' => 'debit',
                    'amount' => $request->debit_amounts[$index],
                    'description' => $request->transaction_details,
                ]);
            }

            // Create credit lines
            foreach ($request->credit_accounts as $index => $accountId) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $accountId,
                    'entry_type' => 'credit',
                    'amount' => $request->credit_amounts[$index],
                    'description' => $request->transaction_details,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.accounting.posted-entries.index')
                ->with('success', 'Journal entry posted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to post journal entry: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display a listing of posted entries
     */
    public function postedEntries(Request $request)
    {
        $query = JournalEntry::with(['creator', 'poster', 'lines.account'])
            ->where('status', 'posted')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        if ($request->filled('month')) {
            $month = Carbon::parse($request->month)->format('Y-m');
            $query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$month]);
        }

        if ($request->filled('day')) {
            $query->whereDate('transaction_date', $request->day);
        }

        if ($request->filled('branch')) {
            $query->where('branch_name', $request->branch);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('transaction_details', 'like', "%{$search}%");
            });
        }

        $entries = $query->paginate(20)->withQueryString();

        // Get unique years and branches for filters
        $years = JournalEntry::selectRaw('YEAR(transaction_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        $branches = JournalEntry::select('branch_name')
            ->distinct()
            ->orderBy('branch_name')
            ->pluck('branch_name');

        return view('admin.accounting.posted-entries.index', compact('entries', 'years', 'branches'));
    }

    /**
     * Display the chart of accounts
     */
    public function chartOfAccounts()
    {
        $accountTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->groupBy('type');
        
        $rules = AccountingRule::with(['debitAccount', 'creditAccount'])
            ->orderBy('name')
            ->get();
        
        $walletMappings = WalletAccountMapping::with('account')->get()->keyBy('wallet_type');
        
        $walletTypes = [
            'savings' => 'Savings Account',
            'transactional' => 'Transactional Account',
            'investment' => 'Investment Account',
            'withdrawals_suspense' => 'Withdrawals Suspense Account',
            'investors_roi' => 'Investors ROI Account',
            'cash' => 'Cash Account',
        ];

        return view('admin.accounting.chart-of-accounts.index', compact(
            'accountTypes',
            'accounts',
            'rules',
            'walletMappings',
            'walletTypes'
        ));
    }

    /**
     * Update wallet account mappings
     */
    public function updateWalletMappings(Request $request)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*' => 'required|exists:chart_of_accounts,id',
        ]);

        DB::beginTransaction();
        try {
            WalletAccountMapping::truncate();
            
            foreach ($request->mappings as $walletType => $accountId) {
                WalletAccountMapping::create([
                    'wallet_type' => $walletType,
                    'account_id' => $accountId,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Wallet account mappings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update mappings: ' . $e->getMessage()]);
        }
    }

    /**
     * Display a listing of expenses
     */
    public function expenses(Request $request)
    {
        // Get expense accounts
        $expenseAccountIds = ChartOfAccount::where('type', 'EXPENSE')
            ->where('is_active', true)
            ->pluck('id');

        $query = JournalEntryLine::with(['journalEntry.creator', 'journalEntry.poster', 'account'])
            ->whereIn('account_id', $expenseAccountIds)
            ->whereHas('journalEntry', function($q) {
                $q->where('status', 'posted');
            })
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->select('journal_entry_lines.*', 'journal_entries.transaction_date', 'journal_entries.reference_number', 'journal_entries.transaction_id', 'journal_entries.branch_name')
            ->orderBy('journal_entries.transaction_date', 'desc')
            ->orderBy('journal_entries.created_at', 'desc');

        // Filters
        if ($request->filled('year')) {
            $query->whereYear('journal_entries.transaction_date', $request->year);
        }

        if ($request->filled('month')) {
            $month = Carbon::parse($request->month)->format('Y-m');
            $query->whereRaw("DATE_FORMAT(journal_entries.transaction_date, '%Y-%m') = ?", [$month]);
        }

        if ($request->filled('day')) {
            $query->whereDate('journal_entries.transaction_date', $request->day);
        }

        if ($request->filled('branch')) {
            $query->where('journal_entries.branch_name', $request->branch);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('journal_entries.transaction_id', 'like', "%{$search}%")
                  ->orWhere('journal_entries.reference_number', 'like', "%{$search}%")
                  ->orWhere('journal_entry_lines.description', 'like', "%{$search}%");
            });
        }

        $expenses = $query->paginate(20)->withQueryString();
        
        // Reload relationships after join (they are lost when using select)
        $expenses->getCollection()->load(['journalEntry.creator', 'journalEntry.poster', 'account']);

        // Calculate total
        $total = JournalEntryLine::whereIn('account_id', $expenseAccountIds)
            ->whereHas('journalEntry', function($q) use ($request) {
                $q->where('status', 'posted');
                if ($request->filled('year')) {
                    $q->whereYear('transaction_date', $request->year);
                }
                if ($request->filled('month')) {
                    $month = Carbon::parse($request->month)->format('Y-m');
                    $q->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$month]);
                }
                if ($request->filled('day')) {
                    $q->whereDate('transaction_date', $request->day);
                }
                if ($request->filled('branch')) {
                    $q->where('branch_name', $request->branch);
                }
            })
            ->sum('amount');

        // Get unique years and branches for filters
        $years = JournalEntry::selectRaw('YEAR(transaction_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        $branches = JournalEntry::select('branch_name')
            ->distinct()
            ->orderBy('branch_name')
            ->pluck('branch_name');

        return view('admin.accounting.expenses.index', compact('expenses', 'total', 'years', 'branches'));
    }

    /**
     * Display the general ledger
     */
    public function ledger(Request $request)
    {
        // Build query for journal entry lines
        $query = JournalEntryLine::with(['journalEntry.creator', 'journalEntry.poster', 'account'])
            ->whereHas('journalEntry', function($q) {
                $q->where('status', 'posted');
            });

        // Filter by account if provided
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // Filter by branch if provided
        if ($request->filled('branch')) {
            $query->whereHas('journalEntry', function($q) use ($request) {
                $q->where('branch_name', $request->branch);
            });
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereHas('journalEntry', function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->from);
            });
        }

        if ($request->filled('to')) {
            $query->whereHas('journalEntry', function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->to);
            });
        }

        // Get entries with proper ordering
        $entries = $query
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->select('journal_entry_lines.*')
            ->orderBy('journal_entries.transaction_date', 'asc')
            ->orderBy('journal_entries.created_at', 'asc')
            ->orderBy('journal_entry_lines.id', 'asc')
            ->get();
        
        // Reload relationships after join
        $entries->load(['journalEntry.creator', 'journalEntry.poster', 'account']);

        // Calculate running balance per account
        // Group entries by account first if showing all accounts
        $balances = [];
        $ledgerData = [];
        $currentAccountId = null;
        
        foreach ($entries as $entry) {
            $accountId = $entry->account_id;
            
            // If account changed and we're showing all accounts, reset balance for new account
            if ($currentAccountId !== null && $currentAccountId !== $accountId && !$request->filled('account_id')) {
                // Account changed - this is for visual separation if needed
            }
            $currentAccountId = $accountId;
            
            // Initialize balance for account if not exists
            if (!isset($balances[$accountId])) {
                $balances[$accountId] = 0;
            }
            
            // Calculate balance based on entry type and account type
            // ASSET and EXPENSE: debit increases, credit decreases
            // LIABILITY, EQUITY, INCOME: credit increases, debit decreases
            $accountType = $entry->account->type;
            
            if ($entry->entry_type === 'debit') {
                if (in_array($accountType, ['ASSET', 'EXPENSE'])) {
                    $balances[$accountId] += $entry->amount;
                } else {
                    $balances[$accountId] -= $entry->amount;
                }
            } else { // credit
                if (in_array($accountType, ['ASSET', 'EXPENSE'])) {
                    $balances[$accountId] -= $entry->amount;
                } else {
                    $balances[$accountId] += $entry->amount;
                }
            }
            
            $ledgerData[] = [
                'entry' => $entry,
                'balance' => $balances[$accountId],
                'account_code' => $entry->account->code,
                'account_name' => $entry->account->name,
            ];
        }

        // Get accounts for filter dropdown
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get();

        // Get unique branches for filter
        $branches = JournalEntry::select('branch_name')
            ->distinct()
            ->orderBy('branch_name')
            ->pluck('branch_name');

        return view('admin.accounting.ledger.index', compact('ledgerData', 'accounts', 'branches'));
    }

    /**
     * Display accruals and reports
     */
    public function reports()
    {
        // TODO: Implement reports logic
        return view('admin.accounting.reports.index');
    }

    /**
     * Display budget reports
     */
    public function budget()
    {
        // TODO: Implement budget reports logic
        return view('admin.accounting.budget.index');
    }

    /**
     * Display company assets
     */
    public function assets()
    {
        // TODO: Implement assets listing logic
        return view('admin.accounting.assets.index');
    }

    /**
     * Display accounts reconciliation
     */
    public function reconciliation()
    {
        // TODO: Implement reconciliation logic
        return view('admin.accounting.reconciliation.index');
    }

    /**
     * Display employee payroll
     */
    public function payroll()
    {
        // TODO: Implement payroll logic
        return view('admin.accounting.payroll.index');
    }
}
