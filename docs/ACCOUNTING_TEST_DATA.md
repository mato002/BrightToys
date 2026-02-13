# Accounting Test Data Seeder

This document describes the test data that is seeded by the `AccountingSeeder` for testing the accounting modules.

## Running the Seeder

To seed accounting test data, run:

```bash
php artisan db:seed --class=AccountingSeeder
```

Or include it in the main seeder:

```bash
php artisan db:seed
```

## What Gets Seeded

### 1. Chart of Accounts

The seeder creates a complete chart of accounts with the following structure:

#### ASSETS
- **1000** - Cash and Cash Equivalents
  - **1010** - Mpesa Bulk Utility (sub-account)
- **1100** - Employee Advances
- **1200** - Loanbook
  - **1210** - Prepaid Loans (sub-account)
- **1300** - Accounts Receivable

#### LIABILITIES
- **2000** - Loan Overpayments

#### EQUITY
- **3000** - Partner Equity

#### INCOME
- **4000** - Interest Income
- **4100** - Sales Revenue

#### EXPENSES
- **5000** - Salaries and Wages
- **5100** - Rent Expense
- **5200** - Utilities
- **5300** - Marketing and Advertising

### 2. Accounting Rules

Four accounting rules are created for automatic journal entry generation:

1. **Salary Advances** - Creates entries when salary advances are processed
2. **Loan Ledger** - Creates entries for loan disbursements
3. **Loan Overpayments** - Handles loan overpayment transactions
4. **Loan Interests** - Accrues interest income from loans

### 3. Wallet Account Mappings

Maps wallet types to chart of accounts:
- `savings` → Cash Account
- `transactional` → Cash Account
- `investment` → Partner Equity
- `withdrawals_suspense` → Cash Account
- `investors_roi` → Partner Equity
- `cash` → Cash Account

### 4. Journal Entries

Seven sample journal entries are created with balanced debit/credit entries:

1. **JE-2026-001** - Initial capital injection (500,000)
2. **JE-2026-002** - Salary advance (50,000)
3. **JE-2026-003** - Loan disbursement (100,000)
4. **JE-2026-004** - Loan interest accrual (5,000)
5. **JE-2026-005** - Rent payment (30,000)
6. **JE-2026-006** - Utilities payment (15,000)
7. **JE-2026-007** - Sales revenue (75,000)

All entries are marked as "posted" and dated within the last 30 days.

### 5. Budgets

Four budget entries for the current month:
- **Rent Expense**: Budget 30,000 / Spent 30,000
- **Utilities**: Budget 20,000 / Spent 15,000
- **Marketing**: Budget 50,000 / Spent 25,000
- **Salaries**: Budget 200,000 / Spent 180,000

### 6. Company Assets

Four company assets are created:

1. **AST-001** - Office Building
   - Type: Fixed Asset
   - Purchase Value: 5,000,000
   - Current Value: 4,500,000
   - Depreciation: 5% straight-line

2. **AST-002** - Delivery Van
   - Type: Fixed Asset
   - Purchase Value: 1,200,000
   - Current Value: 800,000
   - Depreciation: 20% straight-line

3. **AST-003** - Computer Equipment
   - Type: Fixed Asset
   - Purchase Value: 500,000
   - Current Value: 300,000
   - Depreciation: 33.33% straight-line

4. **AST-004** - Inventory Stock
   - Type: Current Asset
   - Purchase Value: 2,500,000
   - Current Value: 2,500,000

### 7. Account Reconciliations

Three reconciliation records:

1. **Cash Account** (30 days ago) - Reconciled
   - Opening: 0
   - Closing: 500,000
   - Status: Reconciled

2. **Mpesa Account** (15 days ago) - Reconciled
   - Opening: 500,000
   - Closing: 400,000
   - Status: Reconciled

3. **Cash Account** (1 day ago) - Pending
   - Opening: 400,000
   - Closing: 480,000
   - Status: Pending

## Testing the Accounting Modules

After seeding, you can test:

1. **Chart of Accounts** - View all accounts in the admin panel
2. **Journal Entries** - View posted entries and create new ones
3. **Accounting Rules** - Test automatic journal entry creation
4. **Budgets** - View budget vs actual spending
5. **Company Assets** - View asset register and depreciation
6. **Account Reconciliations** - View reconciliation history
7. **Financial Reports** - Generate reports using the seeded data

## Notes

- The seeder uses `firstOrCreate` to avoid duplicates if run multiple times
- All dates are relative to the current date (using Carbon)
- All amounts are in Kenyan Shillings (KES)
- The seeder requires an admin user to exist (creates one if missing)
