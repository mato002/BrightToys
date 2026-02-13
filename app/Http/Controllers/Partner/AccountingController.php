<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Budget;
use App\Models\CompanyAsset;
use App\Models\AccountReconciliation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountingController extends Controller
{
    /**
     * Display the accounting dashboard (read-only for partners)
     */
    public function dashboard()
    {
        $user = auth()->user();
        $partner = $user->partner;

        // Get summary statistics
        $totalEntries = JournalEntry::where('status', 'posted')->count();
        $totalAccounts = ChartOfAccount::where('is_active', true)->count();
        
        // Get recent journal entries
        $recentEntries = JournalEntry::where('status', 'posted')
            ->with(['creator', 'poster'])
            ->latest('transaction_date')
            ->take(10)
            ->get();

        // Get account balances summary
        $accountBalances = ChartOfAccount::where('is_active', true)
            ->with(['journalEntryLines' => function($query) {
                $query->whereHas('journalEntry', function($q) {
                    $q->where('status', 'posted');
                });
            }])
            ->get()
            ->map(function($account) {
                $debits = $account->journalEntryLines->where('entry_type', 'debit')->sum('amount');
                $credits = $account->journalEntryLines->where('entry_type', 'credit')->sum('amount');
                
                // Calculate balance based on account type
                $balance = 0;
                if (in_array($account->type, ['ASSET', 'EXPENSE'])) {
                    $balance = $debits - $credits;
                } else {
                    $balance = $credits - $debits;
                }
                
                return [
                    'account' => $account,
                    'balance' => $balance,
                    'debits' => $debits,
                    'credits' => $credits,
                ];
            })
            ->sortByDesc('balance')
            ->take(10);

        // Get monthly transaction summary
        $monthlySummary = JournalEntry::where('status', 'posted')
            ->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, COUNT(*) as count, SUM((SELECT SUM(amount) FROM journal_entry_lines WHERE journal_entry_lines.journal_entry_id = journal_entries.id AND journal_entry_lines.entry_type = "debit")) as total_debits')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get()
            ->map(function($item) {
                return [
                    'period' => Carbon::create($item->year, $item->month, 1)->format('M Y'),
                    'count' => $item->count,
                    'total_debits' => $item->total_debits ?? 0,
                ];
            });

        return view('partner.accounting.dashboard', compact(
            'totalEntries',
            'totalAccounts',
            'recentEntries',
            'accountBalances',
            'monthlySummary'
        ));
    }

    /**
     * Display the general ledger (read-only for partners)
     */
    public function ledger(Request $request)
    {
        // Build base query with relationships
        $query = JournalEntryLine::with(['journalEntry.creator', 'journalEntry.poster', 'account'])
            ->whereHas('journalEntry', function($q) {
                $q->where('status', 'posted');
            });

        // Filter by account if provided
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereHas('journalEntry', function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->from_date);
            });
        }

        if ($request->filled('to_date')) {
            $query->whereHas('journalEntry', function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->to_date);
            });
        }

        // Use join for ordering but select all journal_entry_lines columns
        // This ensures we get the data and can still order by journal entry fields
        $entries = $query
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->select('journal_entry_lines.*') // Select all line columns
            ->orderBy('journal_entries.transaction_date', 'desc')
            ->orderBy('journal_entries.created_at', 'desc')
            ->orderBy('journal_entry_lines.id', 'desc')
            ->paginate(50)
            ->withQueryString();
        
        // Reload relationships after join (they are lost when using select)
        // This is critical - without this, journalEntry and account won't be loaded
        $collection = $entries->getCollection();
        $collection->load(['journalEntry.creator', 'journalEntry.poster', 'account']);
        
        // Ensure the collection is set back (though load modifies in place)
        $entries->setCollection($collection);

        // Get accounts for filter dropdown
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('partner.accounting.ledger', compact('entries', 'accounts'));
    }

    /**
     * Display financial reports (read-only for partners)
     */
    public function reports(Request $request)
    {
        $user = auth()->user();
        $partner = $user->partner;

        // Get date range (default to current year)
        $fromDate = $request->filled('from_date') ? $request->from_date : Carbon::now()->startOfYear()->toDateString();
        $toDate = $request->filled('to_date') ? $request->to_date : Carbon::now()->endOfYear()->toDateString();

        // Get account balances by type
        $accountTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];
        $accountsByType = ChartOfAccount::where('is_active', true)
            ->whereIn('type', $accountTypes)
            ->with(['journalEntryLines' => function($query) use ($fromDate, $toDate) {
                $query->whereHas('journalEntry', function($q) use ($fromDate, $toDate) {
                    $q->where('status', 'posted')
                      ->whereBetween('transaction_date', [$fromDate, $toDate]);
                });
            }])
            ->get()
            ->groupBy('type')
            ->map(function($accounts) {
                return $accounts->map(function($account) {
                    $debits = $account->journalEntryLines->where('entry_type', 'debit')->sum('amount');
                    $credits = $account->journalEntryLines->where('entry_type', 'credit')->sum('amount');
                    
                    // Calculate balance based on account type
                    $balance = 0;
                    if (in_array($account->type, ['ASSET', 'EXPENSE'])) {
                        $balance = $debits - $credits;
                    } else {
                        $balance = $credits - $debits;
                    }
                    
                    return [
                        'account' => $account,
                        'balance' => $balance,
                        'debits' => $debits,
                        'credits' => $credits,
                    ];
                });
            });

        // Calculate totals by type
        $totalsByType = $accountsByType->map(function($accounts) {
            return $accounts->sum('balance');
        });

        return view('partner.accounting.reports', compact(
            'accountsByType',
            'totalsByType',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Display chart of accounts (read-only for partners)
     */
    public function chartOfAccounts()
    {
        $accountTypes = ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'];
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->groupBy('type');
        
        return view('partner.accounting.chart-of-accounts', compact(
            'accountTypes',
            'accounts'
        ));
    }

    /**
     * Display posted journal entries (read-only for partners)
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

        return view('partner.accounting.posted-entries', compact('entries', 'years', 'branches'));
    }

    /**
     * Display expenses (read-only for partners)
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

        return view('partner.accounting.expenses', compact('expenses', 'total', 'years', 'branches'));
    }

    /**
     * Display budget reports (read-only for partners)
     */
    public function budget(Request $request)
    {
        $query = Budget::with('account')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        // Filters
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('branch')) {
            $query->where('branch_name', $request->branch);
        }

        $budgets = $query->paginate(20)->withQueryString();

        // Get summary statistics
        $totalBudget = Budget::sum('budget_amount');
        $totalSpent = Budget::sum('spent_amount');
        $totalBalance = $totalBudget - $totalSpent;

        // Get filter options
        $years = Budget::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $accounts = ChartOfAccount::where('type', 'EXPENSE')->where('is_active', true)->orderBy('code')->get();
        $branches = Budget::select('branch_name')->distinct()->orderBy('branch_name')->pluck('branch_name');

        return view('partner.accounting.budget', compact(
            'budgets',
            'totalBudget',
            'totalSpent',
            'totalBalance',
            'years',
            'accounts',
            'branches'
        ));
    }

    /**
     * Display company assets (read-only for partners)
     */
    public function assets(Request $request)
    {
        $query = CompanyAsset::with(['account', 'creator'])
            ->orderBy('purchase_date', 'desc');

        // Filters
        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        $assets = $query->paginate(20)->withQueryString();

        // Get summary statistics
        $totalPurchaseValue = CompanyAsset::where('is_active', true)->sum('purchase_value');
        $totalCurrentValue = CompanyAsset::where('is_active', true)->sum('current_value');
        $totalAssets = CompanyAsset::where('is_active', true)->count();

        // Get filter options
        $assetTypes = ['fixed', 'current', 'intangible', 'other'];

        return view('partner.accounting.assets', compact(
            'assets',
            'totalPurchaseValue',
            'totalCurrentValue',
            'totalAssets',
            'assetTypes'
        ));
    }

    /**
     * Display accounts reconciliation (read-only for partners)
     */
    public function reconciliation(Request $request)
    {
        $query = AccountReconciliation::with(['account', 'reconciler'])
            ->orderBy('reconciliation_date', 'desc');

        // Filters
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->where('reconciliation_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('reconciliation_date', '<=', $request->to_date);
        }

        $reconciliations = $query->paginate(20)->withQueryString();

        // Get summary statistics
        $totalReconciled = AccountReconciliation::where('status', 'reconciled')->count();
        $totalPending = AccountReconciliation::where('status', 'pending')->count();
        $totalDiscrepancies = AccountReconciliation::where('status', 'discrepancy')->count();

        // Get filter options
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        $statuses = ['pending', 'reconciled', 'discrepancy'];

        return view('partner.accounting.reconciliation', compact(
            'reconciliations',
            'totalReconciled',
            'totalPending',
            'totalDiscrepancies',
            'accounts',
            'statuses'
        ));
    }

    /**
     * Display employee payroll (read-only for partners)
     */
    public function payroll()
    {
        // Note: Payroll functionality would typically require a separate payroll module
        // For now, we'll show a read-only view with placeholder information
        // This can be expanded when payroll module is implemented
        
        return view('partner.accounting.payroll');
    }

    /**
     * Display financial overview (read-only for partners)
     */
    public function financialOverview()
    {
        // TODO: Replace these placeholders with real aggregates
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

        return view('partner.accounting.financial-overview', compact(
            'groupSummary',
            'bankBalances',
            'outstandingLoans',
            'assetsSummary',
            'performance',
            'welfareStats'
        ));
    }
}
