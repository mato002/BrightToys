# Where to See Contributions in the Accounting Module

When a partner contribution is **approved**, it automatically creates a journal entry in the accounting system. Here's where you can find it:

## 1. **Posted Entries** (Primary Location)

**Admin Path:** `Accounting > Posted Entries`  
**Partner Path:** `Accounting > Posted Entries`

This is the main place to see all journal entries, including contributions.

**What you'll see:**
- Transaction ID (auto-generated)
- Reference Number: `CONT-{contribution_id}` or the contribution's reference number
- Transaction Details: "Partner {investment/welfare} contribution from {Partner Name}"
- Transaction Date: The contribution date
- Status: Posted
- Debit/Credit lines showing:
  - **Debit:** Cash Account (1000) - Cash and Cash Equivalents
  - **Credit:** Partner Equity (3000) - Partner Equity

**How to identify contributions:**
- Look for reference numbers starting with "CONT-"
- Description mentions "contribution from"
- Comments say "Auto-posted from partner contribution #{id}"

## 2. **General Ledger**

**Admin Path:** `Accounting > General Ledger`  
**Partner Path:** `Accounting > Ledger`

Shows all accounting entries with running balances.

**What you'll see:**
- Date of the contribution
- Description of the transaction
- Account affected (Cash or Equity)
- Debit amount (in Cash account)
- Credit amount (in Equity account)
- Running balance for each account

**Filtering:**
- Filter by account to see only Cash or Equity entries
- Filter by date range to see contributions in a specific period
- Filter by branch (usually "Corporate (HQ)")

## 3. **Chart of Accounts**

**Admin Path:** `Accounting > Chart of Accounts`  
**Partner Path:** `Accounting > Chart of Accounts`

Shows account balances that are affected by contributions.

**Accounts affected:**
- **1000 - Cash and Cash Equivalents** (Debit increases)
- **3000 - Partner Equity** (Credit increases)

**What you'll see:**
- Updated balances for Cash and Equity accounts
- Transaction history for each account
- Debit and credit totals

## 4. **Financial Overview**

**Admin Path:** `Accounting > Financial Overview`  
**Partner Path:** `Accounting > Financial Overview`

Shows high-level financial summaries.

**What you'll see:**
- Total investment contributions
- Investment wallets total
- Net worth calculations
- Overall financial health

## 5. **Company Expenses** (For Withdrawals Only)

**Admin Path:** `Accounting > Company Expenses`  
**Partner Path:** `Accounting > Expenses`

Only appears here if the contribution type is a **withdrawal** or **profit distribution** (not regular contributions).

## How Contributions Are Recorded

When a contribution is approved, the system automatically:

1. **Creates a Journal Entry** with:
   - Transaction ID (auto-generated, format: YYYYMMDD####)
   - Reference: Contribution reference or "CONT-{id}"
   - Date: Contribution date
   - Status: Posted (immediately)

2. **Creates Two Journal Entry Lines:**
   - **Debit Line:** Cash Account (1000) - increases cash
   - **Credit Line:** Partner Equity (3000) - increases equity

3. **Updates Account Balances:**
   - Cash account balance increases
   - Partner Equity balance increases

## Example Transaction

**Contribution:** Partner invests 70,000 KES

**Journal Entry Created:**
```
Transaction ID: 20260213####
Reference: CONT-123
Date: 2026-02-13
Description: Partner investment contribution from Partner One

Debit:  Cash Account (1000)       70,000.00
Credit: Partner Equity (3000)     70,000.00
```

## Finding a Specific Contribution

To find a specific contribution in the accounting module:

1. **By Reference Number:**
   - Go to Posted Entries
   - Search for "CONT-{contribution_id}" or the contribution's reference number

2. **By Date:**
   - Go to General Ledger
   - Filter by the contribution date
   - Look for entries on that date

3. **By Amount:**
   - Go to General Ledger
   - Look for the exact contribution amount
   - Check both Cash (debit) and Equity (credit) columns

4. **By Account:**
   - Go to Chart of Accounts
   - Click on Cash Account (1000) or Partner Equity (3000)
   - View transaction history

## Troubleshooting

**If you don't see a contribution:**

1. **Check if it's approved:**
   - Only approved contributions create journal entries
   - Pending contributions won't appear

2. **Check the date range:**
   - Make sure your filters include the contribution date

3. **Check the account:**
   - Contributions affect Cash (1000) and Equity (3000)
   - Filter by these accounts if needed

4. **Check the reference:**
   - Look for "CONT-" prefix in reference numbers
   - Or search by the contribution's original reference

## Summary

**Best places to see contributions:**
1. ✅ **Posted Entries** - Complete journal entry details
2. ✅ **General Ledger** - With running balances
3. ✅ **Chart of Accounts** - Account balance changes
4. ✅ **Financial Overview** - High-level summaries

All contributions are automatically posted when approved, so they should appear immediately in all these views!
