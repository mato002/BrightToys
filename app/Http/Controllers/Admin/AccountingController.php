<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\AccountingRule;
use App\Models\WalletAccountMapping;
use App\Models\Loan;
use App\Models\FinancialRecord;
use App\Models\PartnerContribution;
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
        // Get group financial snapshot from service
        $snapshot = \App\Services\FinancialOverviewService::getGroupSnapshot();
        
        // Group Financial Summary
        $groupSummary = [
            'total_contributions' => ($snapshot['total_contributions_investment'] ?? 0) + ($snapshot['total_contributions_welfare'] ?? 0),
            'welfare_total'       => $snapshot['welfare_balance'] ?? 0,
            'investment_total'    => $snapshot['investment_wallet_total'] ?? 0,
            'net_worth'           => $snapshot['net_worth'] ?? 0,
        ];

        // Bank & SACCO Balances - from Chart of Accounts (asset accounts that might be bank accounts)
        $bankAccounts = ChartOfAccount::where('type', 'asset')
            ->where(function($q) {
                $q->where('name', 'like', '%bank%')
                  ->orWhere('name', 'like', '%sacco%')
                  ->orWhere('name', 'like', '%account%');
            })
            ->where('is_active', true)
            ->get();
        
        $bankBalances = $bankAccounts->map(function($account) {
            // Calculate balance from journal entries
            $debits = \App\Models\JournalEntryLine::where('account_id', $account->id)
                ->where('entry_type', 'debit')
                ->sum('amount');
            $credits = \App\Models\JournalEntryLine::where('account_id', $account->id)
                ->where('entry_type', 'credit')
                ->sum('amount');
            $balance = $debits - $credits;
            
            // Get latest reconciliation
            $latestReconciliation = \App\Models\AccountReconciliation::where('account_id', $account->id)
                ->where('status', 'completed')
                ->latest('reconciliation_date')
                ->first();
            
            // Calculate unreconciled amount
            $reconciledAmount = $latestReconciliation ? $latestReconciliation->reconciled_amount : 0;
            $unreconciled = max(0, $balance - $reconciledAmount);
            
            return [
                'name' => $account->name,
                'reconciled' => max(0, $reconciledAmount),
                'unreconciled' => $unreconciled,
            ];
        })->toArray();

        // Outstanding Loans
        $loans = \App\Models\Loan::whereIn('status', ['active', 'pending'])
            ->with(['repayments'])
            ->get();
        
        $outstandingLoans = $loans->map(function($loan) {
            // Calculate principal repaid from repayments
            $totalRepaid = $loan->repayments->sum('amount_paid');
            $outstandingPrincipal = max(0, $loan->amount - $totalRepaid);
            
            // Calculate interest from loan schedules
            $loan->load('schedules.repayments');
            $totalInterestDue = $loan->schedules->sum('interest_due');
            $interestPaid = $loan->schedules->sum(function($schedule) {
                return $schedule->repayments->sum('amount_paid') * ($schedule->interest_due / max(1, $schedule->total_due));
            });
            
            // If no schedules, use simple calculation
            if ($totalInterestDue == 0) {
                $monthsElapsed = \Carbon\Carbon::parse($loan->start_date)->diffInMonths(now());
                $totalInterestDue = ($loan->amount * $loan->interest_rate / 100) * ($monthsElapsed / 12);
            }
            
            $outstandingInterest = max(0, $totalInterestDue - $interestPaid);
            
            return [
                'name' => $loan->lender_name ?? 'Loan #' . $loan->id,
                'principal' => $outstandingPrincipal,
                'interest' => $outstandingInterest,
                'status' => $loan->status === 'active' ? 'on-track' : 'pending',
            ];
        })->toArray();

        // Assets Summary
        $assetsSummary = [
            'land' => $snapshot['assets']['land'] ?? 0,
            'toy_shop' => $snapshot['assets']['toy_shop'] ?? 0,
            'inventory' => $snapshot['assets']['inventory'] ?? 0,
        ];

        // Performance Data
        $performance = [
            'monthly' => $snapshot['performance']['monthly'] ?? [],
            'yearly'  => $snapshot['performance']['yearly'] ?? [],
            'trends'  => $snapshot['performance']['trend'] ?? [],
        ];

        // Welfare Stats
        $welfareContributions = \App\Models\PartnerContribution::where('status', 'approved')
            ->where('is_archived', false)
            ->where('fund_type', 'welfare')
            ->where('type', 'contribution')
            ->sum('amount');
        
        $welfareDisbursements = \App\Models\FinancialRecord::where('type', 'expense')
            ->where('fund_type', 'welfare')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');
        
        // Count pending approvals for display
        $pendingWelfareDisbursements = \App\Models\FinancialRecord::where('type', 'expense')
            ->where('fund_type', 'welfare')
            ->where('status', 'pending_approval')
            ->where('is_archived', false)
            ->count();
        
        // Get all welfare disbursements (both pending and approved) for the table
        $recentWelfareDisbursements = \App\Models\FinancialRecord::where('type', 'expense')
            ->where('fund_type', 'welfare')
            ->where('is_archived', false)
            ->with(['partner', 'creator', 'approver'])
            ->latest('occurred_at')
            ->limit(10)
            ->get()
            ->map(function($record) {
                return [
                    'id' => $record->id,
                    'date' => $record->occurred_at->format('M d, Y'),
                    'member' => $record->partner->name ?? 'N/A',
                    'purpose' => $record->description ?? 'Welfare disbursement',
                    'amount' => $record->amount,
                    'status' => $record->status ?? 'pending_approval',
                    'created_by' => $record->creator->name ?? 'N/A',
                    'approved_by' => $record->approver->name ?? null,
                    'approved_at' => $record->approved_at ? $record->approved_at->format('M d, Y') : null,
                ];
            })->toArray();

        $welfareStats = [
            'total_inflows'      => $welfareContributions,
            'total_disbursements' => $welfareDisbursements,
            'remaining_balance'  => $welfareContributions - $welfareDisbursements,
            'recent_disbursements' => $recentWelfareDisbursements,
            'pending_count'      => $pendingWelfareDisbursements,
        ];

        // Get all partners for member search
        $allPartners = \App\Models\Partner::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.accounting.financial-overview', compact(
            'groupSummary',
            'bankBalances',
            'outstandingLoans',
            'assetsSummary',
            'performance',
            'welfareStats',
            'allPartners'
        ));
    }

    /**
     * Search for a member/partner and return their financial details
     */
    public function searchMember(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $query = $request->input('query');
        
        // Search partners by name, email, phone, or national ID
        $partner = \App\Models\Partner::where('status', 'active')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('national_id_number', 'like', "%{$query}%");
            })
            ->with(['wallets', 'contributions', 'ownerships'])
            ->first();

        if (!$partner) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        // Calculate financial details
        $approvedContributions = $partner->contributions()
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->get();

        $totalContributions = $approvedContributions->sum('amount');
        $welfareContributions = $approvedContributions->where('fund_type', 'welfare')->sum('amount');
        $investmentContributions = $approvedContributions->where('fund_type', 'investment')->sum('amount');
        
        // Get wallet balances
        $welfareBalance = \App\Services\WalletService::getWalletBalance($partner, \App\Models\MemberWallet::TYPE_WELFARE);
        $investmentBalance = \App\Services\WalletService::getWalletBalance($partner, \App\Models\MemberWallet::TYPE_INVESTMENT);
        
        // Get total investment from all partners for percentage calculation
        $totalGroupInvestment = \App\Models\MemberWallet::where('type', \App\Models\MemberWallet::TYPE_INVESTMENT)->sum('balance');
        $investmentShare = $totalGroupInvestment > 0 ? ($investmentBalance / $totalGroupInvestment) * 100 : 0;
        
        // Get ownership percentage
        $ownershipPercentage = $partner->getCurrentOwnershipPercentage();
        
        // Get profit distributions
        $profitDistributions = $approvedContributions
            ->where('type', 'profit_distribution')
            ->sum('amount');

        return response()->json([
            'partner' => [
                'id' => $partner->id,
                'name' => $partner->name,
                'email' => $partner->email,
                'phone' => $partner->phone,
            ],
            'financials' => [
                'total_contributed' => $totalContributions,
                'welfare_contributions' => $welfareContributions,
                'investment_contributions' => $investmentContributions,
                'welfare_balance' => $welfareBalance,
                'investment_balance' => $investmentBalance,
                'investment_share_percent' => round($investmentShare, 2),
                'ownership_percentage' => $ownershipPercentage,
                'profit_entitlement' => $profitDistributions,
            ],
        ]);
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
     * Display accruals and reports (Income Statement, Trial Balance, Balance Sheet)
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('type', 'income');
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        
        $startDate = Carbon::create($year, $month ?? 1, 1)->startOfMonth();
        $endDate = $month 
            ? Carbon::create($year, $month, 1)->endOfMonth()
            : Carbon::create($year, 12, 31)->endOfYear();
        
        $previousStartDate = $startDate->copy()->subYear()->startOfMonth();
        $previousEndDate = $endDate->copy()->subYear()->endOfMonth();
        
        $data = [];
        
        if ($reportType === 'income') {
            // Income Statement
            $revenueAccounts = ChartOfAccount::where('type', 'revenue')
                ->where('is_active', true)
                ->get();
            
            $expenseAccounts = ChartOfAccount::where('type', 'expense')
                ->where('is_active', true)
                ->get();
            
            $revenues = [];
            $expenses = [];
            
            foreach ($revenueAccounts as $account) {
                $current = $this->getAccountBalance($account->id, $startDate, $endDate);
                $previous = $this->getAccountBalance($account->id, $previousStartDate, $previousEndDate);
                if ($current > 0 || $previous > 0) {
                    $revenues[] = [
                        'name' => $account->name,
                        'current' => $current,
                        'previous' => $previous,
                    ];
                }
            }
            
            foreach ($expenseAccounts as $account) {
                $current = abs($this->getAccountBalance($account->id, $startDate, $endDate));
                $previous = abs($this->getAccountBalance($account->id, $previousStartDate, $previousEndDate));
                if ($current > 0 || $previous > 0) {
                    $expenses[] = [
                        'name' => $account->name,
                        'current' => $current,
                        'previous' => $previous,
                    ];
                }
            }
            
            $totalRevenue = collect($revenues)->sum('current');
            $totalExpenses = collect($expenses)->sum('current');
            $netIncome = $totalRevenue - $totalExpenses;
            
            $data = [
                'type' => 'income',
                'revenues' => $revenues,
                'expenses' => $expenses,
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'previous_revenue' => collect($revenues)->sum('previous'),
                'previous_expenses' => collect($expenses)->sum('previous'),
                'previous_net_income' => collect($revenues)->sum('previous') - collect($expenses)->sum('previous'),
            ];
        } elseif ($reportType === 'trial_balance') {
            // Trial Balance
            $accounts = ChartOfAccount::where('is_active', true)
                ->orderBy('code')
                ->get();
            
            $trialBalance = [];
            foreach ($accounts as $account) {
                $debits = JournalEntryLine::where('account_id', $account->id)
                    ->where('entry_type', 'debit')
                    ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('transaction_date', [$startDate, $endDate]);
                    })
                    ->sum('amount');
                
                $credits = JournalEntryLine::where('account_id', $account->id)
                    ->where('entry_type', 'credit')
                    ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('transaction_date', [$startDate, $endDate]);
                    })
                    ->sum('amount');
                
                if ($debits > 0 || $credits > 0) {
                    $trialBalance[] = [
                        'code' => $account->code,
                        'name' => $account->name,
                        'debits' => $debits,
                        'credits' => $credits,
                    ];
                }
            }
            
            $data = [
                'type' => 'trial_balance',
                'accounts' => $trialBalance,
                'total_debits' => collect($trialBalance)->sum('debits'),
                'total_credits' => collect($trialBalance)->sum('credits'),
            ];
        } elseif ($reportType === 'balance_sheet') {
            // Balance Sheet
            $assets = ChartOfAccount::where('type', 'asset')
                ->where('is_active', true)
                ->get()
                ->map(function($account) use ($endDate) {
                    $balance = $this->getAccountBalance($account->id, null, $endDate);
                    return [
                        'name' => $account->name,
                        'balance' => $balance,
                    ];
                })
                ->filter(fn($a) => $a['balance'] > 0);
            
            $liabilities = ChartOfAccount::where('type', 'liability')
                ->where('is_active', true)
                ->get()
                ->map(function($account) use ($endDate) {
                    $balance = abs($this->getAccountBalance($account->id, null, $endDate));
                    return [
                        'name' => $account->name,
                        'balance' => $balance,
                    ];
                })
                ->filter(fn($l) => $l['balance'] > 0);
            
            $equity = ChartOfAccount::where('type', 'equity')
                ->where('is_active', true)
                ->get()
                ->map(function($account) use ($endDate) {
                    $balance = abs($this->getAccountBalance($account->id, null, $endDate));
                    return [
                        'name' => $account->name,
                        'balance' => $balance,
                    ];
                })
                ->filter(fn($e) => $e['balance'] > 0);
            
            $totalAssets = $assets->sum('balance');
            $totalLiabilities = $liabilities->sum('balance');
            $totalEquity = $equity->sum('balance');
            
            $data = [
                'type' => 'balance_sheet',
                'assets' => $assets->values(),
                'liabilities' => $liabilities->values(),
                'equity' => $equity->values(),
                'total_assets' => $totalAssets,
                'total_liabilities' => $totalLiabilities,
                'total_equity' => $totalEquity,
            ];
        }
        
        return view('admin.accounting.reports.index', compact('data', 'reportType', 'year', 'month', 'startDate', 'endDate'));
    }
    
    /**
     * Helper method to get account balance for a date range
     */
    private function getAccountBalance($accountId, $startDate = null, $endDate = null)
    {
        $query = JournalEntryLine::where('account_id', $accountId);
        
        if ($startDate && $endDate) {
            $query->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            });
        } elseif ($endDate) {
            $query->whereHas('journalEntry', function($q) use ($endDate) {
                $q->where('transaction_date', '<=', $endDate);
            });
        }
        
        $debits = (clone $query)->where('entry_type', 'debit')->sum('amount');
        $credits = (clone $query)->where('entry_type', 'credit')->sum('amount');
        
        // For asset and expense accounts, debits increase, credits decrease
        // For liability, equity, and revenue accounts, credits increase, debits decrease
        $account = ChartOfAccount::find($accountId);
        if (in_array($account->type, ['asset', 'expense'])) {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }

    /**
     * Display budget reports
     */
    public function budget(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        $budgets = \App\Models\Budget::where('year', $year)
            ->where('month', $month)
            ->with('account')
            ->get();
        
        $budgetData = [];
        foreach ($budgets as $budget) {
            $actual = $this->getAccountBalance($budget->account_id, 
                Carbon::create($year, $month, 1)->startOfMonth(),
                Carbon::create($year, $month, 1)->endOfMonth()
            );
            $variance = $budget->amount - abs($actual);
            $variancePercent = $budget->amount > 0 ? ($variance / $budget->amount) * 100 : 0;
            
            $budgetData[] = [
                'account' => $budget->account->name,
                'budgeted' => $budget->amount,
                'actual' => abs($actual),
                'variance' => $variance,
                'variance_percent' => $variancePercent,
            ];
        }
        
        return view('admin.accounting.budget.index', compact('budgetData', 'year', 'month'));
    }

    /**
     * Display company assets
     */
    public function assets()
    {
        $assets = \App\Models\ProjectAsset::with(['project', 'creator'])
            ->orderBy('date_acquired', 'desc')
            ->get()
            ->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'code' => 'AST-' . str_pad($asset->id, 6, '0', STR_PAD_LEFT),
                    'name' => $asset->name,
                    'type' => ucfirst(str_replace('_', ' ', $asset->category ?? 'other')),
                    'purchase_value' => $asset->acquisition_cost,
                    'current_value' => $asset->current_value,
                    'location' => $asset->project->name ?? 'N/A',
                    'date_acquired' => $asset->date_acquired,
                ];
            });
        
        return view('admin.accounting.assets.index', compact('assets'));
    }

    /**
     * Display accounts reconciliation
     */
    public function reconciliation(Request $request)
    {
        $accountId = $request->get('account_id');
        
        $accounts = ChartOfAccount::where('type', 'asset')
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('name', 'like', '%bank%')
                  ->orWhere('name', 'like', '%sacco%')
                  ->orWhere('name', 'like', '%account%');
            })
            ->get();
        
        $reconciliations = [];
        if ($accountId) {
            $account = ChartOfAccount::find($accountId);
            if ($account) {
                $reconciliations = \App\Models\AccountReconciliation::where('account_id', $accountId)
                    ->orderBy('reconciliation_date', 'desc')
                    ->with('reconciler')
                    ->get();
            }
        }
        
        return view('admin.accounting.reconciliation.index', compact('accounts', 'reconciliations', 'accountId'));
    }

    /**
     * Display employee payroll
     */
    public function payroll(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // Get payroll expenses from financial records
        $payrollRecords = FinancialRecord::where('type', 'expense')
            ->where(function($q) {
                $q->where('category', 'like', '%salary%')
                  ->orWhere('category', 'like', '%payroll%')
                  ->orWhere('category', 'like', '%wage%');
            })
            ->whereYear('occurred_at', $year)
            ->whereMonth('occurred_at', $month)
            ->where('status', 'approved')
            ->with('partner')
            ->get();
        
        $payrollData = $payrollRecords->map(function($record) {
            return [
                'employee' => $record->partner->name ?? $record->description ?? 'Employee',
                'amount' => $record->amount,
                'date' => $record->occurred_at->format('M d, Y'),
                'description' => $record->description,
            ];
        });
        
        $totalPayroll = $payrollData->sum('amount');
        
        return view('admin.accounting.payroll.index', compact('payrollData', 'totalPayroll', 'year', 'month'));
    }
}
