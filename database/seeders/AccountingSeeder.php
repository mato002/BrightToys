<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\AccountingRule;
use App\Models\Budget;
use App\Models\CompanyAsset;
use App\Models\AccountReconciliation;
use App\Models\WalletAccountMapping;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AccountingSeeder extends Seeder
{
    /**
     * Seed accounting data for testing
     */
    public function run(): void
    {
        // Get or create admin user for created_by fields
        $admin = User::where('is_admin', true)->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'Finance Admin',
                'email' => 'finance@brighttoys.com',
                'is_admin' => true,
            ]);
        }

        $this->command->info('Seeding Chart of Accounts...');
        $accounts = $this->seedChartOfAccounts($admin);

        $this->command->info('Seeding Accounting Rules...');
        $this->seedAccountingRules($accounts, $admin);

        $this->command->info('Seeding Wallet Account Mappings...');
        $this->seedWalletMappings($accounts);

        $this->command->info('Seeding Journal Entries...');
        $this->seedJournalEntries($accounts, $admin);

        $this->command->info('Seeding Budgets...');
        $this->seedBudgets($accounts, $admin);

        $this->command->info('Seeding Company Assets...');
        $this->seedCompanyAssets($accounts, $admin);

        $this->command->info('Seeding Account Reconciliations...');
        $this->seedAccountReconciliations($accounts, $admin);

        $this->command->info('Accounting data seeded successfully!');
    }

    /**
     * Seed Chart of Accounts
     */
    private function seedChartOfAccounts(User $admin): array
    {
        $accounts = [];

        // ASSETS
        $cashAccount = ChartOfAccount::firstOrCreate(
            ['code' => '1000'],
            [
                'name' => 'Cash and Cash Equivalents',
                'type' => 'ASSET',
                'description' => 'Cash on hand and in bank accounts',
                'is_active' => true,
            ]
        );
        $accounts['cash'] = $cashAccount;

        $mpesaAccount = ChartOfAccount::firstOrCreate(
            ['code' => '1010'],
            [
                'name' => 'Mpesa Bulk Utility',
                'type' => 'ASSET',
                'description' => 'Mpesa bulk payment account',
                'is_active' => true,
                'parent_id' => $cashAccount->id,
            ]
        );
        $accounts['mpesa'] = $mpesaAccount;

        $employeeAdvances = ChartOfAccount::firstOrCreate(
            ['code' => '1100'],
            [
                'name' => 'Employee Advances',
                'type' => 'ASSET',
                'description' => 'Advances given to employees',
                'is_active' => true,
            ]
        );
        $accounts['employee_advances'] = $employeeAdvances;

        $loanbook = ChartOfAccount::firstOrCreate(
            ['code' => '1200'],
            [
                'name' => 'Loanbook',
                'type' => 'ASSET',
                'description' => 'Outstanding loans to members',
                'is_active' => true,
            ]
        );
        $accounts['loanbook'] = $loanbook;

        $prepaidLoans = ChartOfAccount::firstOrCreate(
            ['code' => '1210'],
            [
                'name' => 'Prepaid Loans',
                'type' => 'ASSET',
                'description' => 'Loan overpayments',
                'is_active' => true,
                'parent_id' => $loanbook->id,
            ]
        );
        $accounts['prepaid_loans'] = $prepaidLoans;

        $accountsReceivable = ChartOfAccount::firstOrCreate(
            ['code' => '1300'],
            [
                'name' => 'Accounts Receivable',
                'type' => 'ASSET',
                'description' => 'Amounts owed to the company',
                'is_active' => true,
            ]
        );
        $accounts['accounts_receivable'] = $accountsReceivable;

        // LIABILITIES
        $loanOverpayments = ChartOfAccount::firstOrCreate(
            ['code' => '2000'],
            [
                'name' => 'Loan Overpayments',
                'type' => 'LIABILITY',
                'description' => 'Overpayments on loans',
                'is_active' => true,
            ]
        );
        $accounts['loan_overpayments'] = $loanOverpayments;

        // EQUITY
        $equity = ChartOfAccount::firstOrCreate(
            ['code' => '3000'],
            [
                'name' => 'Partner Equity',
                'type' => 'EQUITY',
                'description' => 'Total partner contributions and equity',
                'is_active' => true,
            ]
        );
        $accounts['equity'] = $equity;

        // INCOME
        $interestIncome = ChartOfAccount::firstOrCreate(
            ['code' => '4000'],
            [
                'name' => 'Interest Income',
                'type' => 'INCOME',
                'description' => 'Interest earned on loans',
                'is_active' => true,
            ]
        );
        $accounts['interest_income'] = $interestIncome;

        $salesRevenue = ChartOfAccount::firstOrCreate(
            ['code' => '4100'],
            [
                'name' => 'Sales Revenue',
                'type' => 'INCOME',
                'description' => 'Revenue from product sales',
                'is_active' => true,
            ]
        );
        $accounts['sales_revenue'] = $salesRevenue;

        // EXPENSES
        $salaries = ChartOfAccount::firstOrCreate(
            ['code' => '5000'],
            [
                'name' => 'Salaries and Wages',
                'type' => 'EXPENSE',
                'description' => 'Employee salaries and wages',
                'is_active' => true,
            ]
        );
        $accounts['salaries'] = $salaries;

        $rent = ChartOfAccount::firstOrCreate(
            ['code' => '5100'],
            [
                'name' => 'Rent Expense',
                'type' => 'EXPENSE',
                'description' => 'Office and shop rent',
                'is_active' => true,
            ]
        );
        $accounts['rent'] = $rent;

        $utilities = ChartOfAccount::firstOrCreate(
            ['code' => '5200'],
            [
                'name' => 'Utilities',
                'type' => 'EXPENSE',
                'description' => 'Electricity, water, internet',
                'is_active' => true,
            ]
        );
        $accounts['utilities'] = $utilities;

        $marketing = ChartOfAccount::firstOrCreate(
            ['code' => '5300'],
            [
                'name' => 'Marketing and Advertising',
                'type' => 'EXPENSE',
                'description' => 'Marketing and promotional expenses',
                'is_active' => true,
            ]
        );
        $accounts['marketing'] = $marketing;

        return $accounts;
    }

    /**
     * Seed Accounting Rules
     */
    private function seedAccountingRules(array $accounts, User $admin): void
    {
        $rules = [
            [
                'name' => 'Salary Advances',
                'trigger_event' => 'salary_advance',
                'debit_account_id' => $accounts['employee_advances']->id,
                'credit_account_id' => $accounts['mpesa']->id,
                'description' => 'Automatic journal entry for salary advances',
                'is_active' => true,
            ],
            [
                'name' => 'Loan Ledger',
                'trigger_event' => 'loan_ledger',
                'debit_account_id' => $accounts['loanbook']->id,
                'credit_account_id' => $accounts['mpesa']->id,
                'description' => 'Automatic journal entry for loan disbursements',
                'is_active' => true,
            ],
            [
                'name' => 'Loan Overpayments',
                'trigger_event' => 'loan_overpayment',
                'debit_account_id' => $accounts['prepaid_loans']->id,
                'credit_account_id' => $accounts['loan_overpayments']->id,
                'description' => 'Automatic journal entry for loan overpayments',
                'is_active' => true,
            ],
            [
                'name' => 'Loan Interests',
                'trigger_event' => 'loan_interest',
                'debit_account_id' => $accounts['accounts_receivable']->id,
                'credit_account_id' => $accounts['interest_income']->id,
                'description' => 'Automatic journal entry for loan interest accrual',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $ruleData) {
            AccountingRule::firstOrCreate(
                [
                    'trigger_event' => $ruleData['trigger_event'],
                ],
                $ruleData
            );
        }
    }

    /**
     * Seed Wallet Account Mappings
     */
    private function seedWalletMappings(array $accounts): void
    {
        $mappings = [
            'savings' => $accounts['cash']->id,
            'transactional' => $accounts['cash']->id,
            'investment' => $accounts['equity']->id,
            'withdrawals_suspense' => $accounts['cash']->id,
            'investors_roi' => $accounts['equity']->id,
            'cash' => $accounts['cash']->id,
        ];

        foreach ($mappings as $walletType => $accountId) {
            WalletAccountMapping::firstOrCreate(
                ['wallet_type' => $walletType],
                ['account_id' => $accountId]
            );
        }
    }

    /**
     * Seed Journal Entries
     */
    private function seedJournalEntries(array $accounts, User $admin): void
    {
        $entries = [
            [
                'transaction_date' => Carbon::now()->subDays(30),
                'reference_number' => 'JE-2026-001',
                'transaction_details' => 'Initial capital injection',
                'branch_name' => 'Corporate (HQ)',
                'lines' => [
                    ['account' => 'cash', 'type' => 'debit', 'amount' => 500000],
                    ['account' => 'equity', 'type' => 'credit', 'amount' => 500000],
                ],
            ],
            [
                'transaction_date' => Carbon::now()->subDays(25),
                'reference_number' => 'JE-2026-002',
                'transaction_details' => 'Salary advance to employee',
                'branch_name' => 'Corporate (HQ)',
                'lines' => [
                    ['account' => 'employee_advances', 'type' => 'debit', 'amount' => 50000],
                    ['account' => 'mpesa', 'type' => 'credit', 'amount' => 50000],
                ],
            ],
            [
                'transaction_date' => Carbon::now()->subDays(20),
                'reference_number' => 'JE-2026-003',
                'transaction_details' => 'Loan disbursement',
                'branch_name' => 'Corporate (HQ)',
                'lines' => [
                    ['account' => 'loanbook', 'type' => 'debit', 'amount' => 100000],
                    ['account' => 'mpesa', 'type' => 'credit', 'amount' => 100000],
                ],
            ],
            [
                'transaction_date' => Carbon::now()->subDays(15),
                'reference_number' => 'JE-2026-004',
                'transaction_details' => 'Loan interest accrual',
                'branch_name' => 'Corporate (HQ)',
                'lines' => [
                    ['account' => 'accounts_receivable', 'type' => 'debit', 'amount' => 5000],
                    ['account' => 'interest_income', 'type' => 'credit', 'amount' => 5000],
                ],
            ],
            [
                'transaction_date' => Carbon::now()->subDays(10),
                'reference_number' => 'JE-2026-005',
                'transaction_details' => 'Rent payment',
                'branch_name' => 'Corporate (HQ)',
                'lines' => [
                    ['account' => 'rent', 'type' => 'debit', 'amount' => 30000],
                    ['account' => 'cash', 'type' => 'credit', 'amount' => 30000],
                ],
            ],
            [
                'transaction_date' => Carbon::now()->subDays(5),
                'reference_number' => 'JE-2026-006',
                'transaction_details' => 'Utilities payment',
                'branch_name' => 'Corporate (HQ)',
                'lines' => [
                    ['account' => 'utilities', 'type' => 'debit', 'amount' => 15000],
                    ['account' => 'cash', 'type' => 'credit', 'amount' => 15000],
                ],
            ],
            [
                'transaction_date' => Carbon::now()->subDays(2),
                'reference_number' => 'JE-2026-007',
                'transaction_details' => 'Sales revenue',
                'branch_name' => 'Corporate (HQ)',
                'lines' => [
                    ['account' => 'cash', 'type' => 'debit', 'amount' => 75000],
                    ['account' => 'sales_revenue', 'type' => 'credit', 'amount' => 75000],
                ],
            ],
        ];

        foreach ($entries as $entryData) {
            $journalEntry = JournalEntry::firstOrCreate(
                [
                    'reference_number' => $entryData['reference_number'],
                ],
                [
                    'transaction_id' => JournalEntry::generateTransactionId(),
                    'transaction_date' => $entryData['transaction_date'],
                    'transaction_details' => $entryData['transaction_details'],
                    'branch_name' => $entryData['branch_name'],
                    'status' => 'posted',
                    'posted_by' => $admin->id,
                    'posted_at' => $entryData['transaction_date'],
                    'created_by' => $admin->id,
                ]
            );

            // Create journal entry lines
            foreach ($entryData['lines'] as $line) {
                JournalEntryLine::firstOrCreate(
                    [
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $accounts[$line['account']]->id,
                        'entry_type' => $line['type'],
                    ],
                    [
                        'amount' => $line['amount'],
                        'description' => $entryData['transaction_details'],
                    ]
                );
            }
        }
    }

    /**
     * Seed Budgets
     */
    private function seedBudgets(array $accounts, User $admin): void
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $budgets = [
            [
                'account' => 'rent',
                'year' => $currentYear,
                'month' => $currentMonth,
                'budget_amount' => 30000,
                'spent_amount' => 30000,
                'branch_name' => 'Corporate (HQ)',
            ],
            [
                'account' => 'utilities',
                'year' => $currentYear,
                'month' => $currentMonth,
                'budget_amount' => 20000,
                'spent_amount' => 15000,
                'branch_name' => 'Corporate (HQ)',
            ],
            [
                'account' => 'marketing',
                'year' => $currentYear,
                'month' => $currentMonth,
                'budget_amount' => 50000,
                'spent_amount' => 25000,
                'branch_name' => 'Corporate (HQ)',
            ],
            [
                'account' => 'salaries',
                'year' => $currentYear,
                'month' => $currentMonth,
                'budget_amount' => 200000,
                'spent_amount' => 180000,
                'branch_name' => 'Corporate (HQ)',
            ],
        ];

        foreach ($budgets as $budgetData) {
            Budget::firstOrCreate(
                [
                    'account_id' => $accounts[$budgetData['account']]->id,
                    'year' => $budgetData['year'],
                    'month' => $budgetData['month'],
                    'branch_name' => $budgetData['branch_name'],
                ],
                [
                    'budget_amount' => $budgetData['budget_amount'],
                    'spent_amount' => $budgetData['spent_amount'],
                    'expense_book' => null, // Optional field
                ]
            );
        }
    }

    /**
     * Seed Company Assets
     */
    private function seedCompanyAssets(array $accounts, User $admin): void
    {
        $assets = [
            [
                'name' => 'Office Building',
                'asset_code' => 'AST-001',
                'asset_type' => 'fixed',
                'account_id' => $accounts['cash']->id,
                'purchase_value' => 5000000,
                'current_value' => 4500000,
                'purchase_date' => Carbon::now()->subYears(2),
                'depreciation_start_date' => Carbon::now()->subYears(2),
                'depreciation_method' => 'straight_line',
                'depreciation_rate' => 5.00,
                'description' => 'Main office building',
                'location' => 'Nairobi, Kenya',
                'is_active' => true,
            ],
            [
                'name' => 'Delivery Van',
                'asset_code' => 'AST-002',
                'asset_type' => 'fixed',
                'account_id' => $accounts['cash']->id,
                'purchase_value' => 1200000,
                'current_value' => 800000,
                'purchase_date' => Carbon::now()->subMonths(18),
                'depreciation_start_date' => Carbon::now()->subMonths(18),
                'depreciation_method' => 'straight_line',
                'depreciation_rate' => 20.00,
                'description' => 'Delivery vehicle for orders',
                'location' => 'Nairobi, Kenya',
                'is_active' => true,
            ],
            [
                'name' => 'Computer Equipment',
                'asset_code' => 'AST-003',
                'asset_type' => 'fixed',
                'account_id' => $accounts['cash']->id,
                'purchase_value' => 500000,
                'current_value' => 300000,
                'purchase_date' => Carbon::now()->subMonths(12),
                'depreciation_start_date' => Carbon::now()->subMonths(12),
                'depreciation_method' => 'straight_line',
                'depreciation_rate' => 33.33,
                'description' => 'Office computers and equipment',
                'location' => 'Nairobi, Kenya',
                'is_active' => true,
            ],
            [
                'name' => 'Inventory Stock',
                'asset_code' => 'AST-004',
                'asset_type' => 'current',
                'account_id' => $accounts['cash']->id,
                'purchase_value' => 2500000,
                'current_value' => 2500000,
                'purchase_date' => Carbon::now()->subMonths(3),
                'description' => 'Current inventory of toys and products',
                'location' => 'Warehouse, Nairobi',
                'is_active' => true,
            ],
        ];

        foreach ($assets as $assetData) {
            CompanyAsset::firstOrCreate(
                ['asset_code' => $assetData['asset_code']],
                array_merge($assetData, ['created_by' => $admin->id])
            );
        }
    }

    /**
     * Seed Account Reconciliations
     */
    private function seedAccountReconciliations(array $accounts, User $admin): void
    {
        $reconciliations = [
            [
                'account' => 'cash',
                'reconciliation_date' => Carbon::now()->subDays(30),
                'opening_balance' => 0,
                'closing_balance' => 500000,
                'reconciled_amount' => 500000,
                'status' => 'reconciled',
                'notes' => 'Initial reconciliation after capital injection',
            ],
            [
                'account' => 'mpesa',
                'reconciliation_date' => Carbon::now()->subDays(15),
                'opening_balance' => 500000,
                'closing_balance' => 400000,
                'reconciled_amount' => 400000,
                'status' => 'reconciled',
                'notes' => 'Monthly Mpesa account reconciliation',
            ],
            [
                'account' => 'cash',
                'reconciliation_date' => Carbon::now()->subDays(1),
                'opening_balance' => 400000,
                'closing_balance' => 480000,
                'reconciled_amount' => 480000,
                'status' => 'pending',
                'notes' => 'Pending reconciliation - awaiting bank statement',
            ],
        ];

        foreach ($reconciliations as $reconData) {
            AccountReconciliation::firstOrCreate(
                [
                    'account_id' => $accounts[$reconData['account']]->id,
                    'reconciliation_date' => $reconData['reconciliation_date'],
                ],
                [
                    'opening_balance' => $reconData['opening_balance'],
                    'closing_balance' => $reconData['closing_balance'],
                    'reconciled_amount' => $reconData['reconciled_amount'],
                    'status' => $reconData['status'],
                    'notes' => $reconData['notes'],
                    'reconciled_by' => $reconData['status'] === 'reconciled' ? $admin->id : null,
                    'reconciled_at' => $reconData['status'] === 'reconciled' ? $reconData['reconciliation_date'] : null,
                ]
            );
        }
    }
}
