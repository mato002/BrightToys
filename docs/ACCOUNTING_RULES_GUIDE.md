# Accounting Rules Implementation Guide

## Overview

The Accounting Rules feature allows you to define automatic journal entries for frequent transactions. This ensures consistent double-entry bookkeeping and reduces manual data entry errors.

## Accounting Standards & Best Practices

### Double-Entry Bookkeeping Principles

1. **Every transaction must have equal debits and credits**
   - Total debits = Total credits
   - This maintains the accounting equation: Assets = Liabilities + Equity

2. **Account Types and Normal Balances:**
   - **ASSET accounts**: Normal balance is DEBIT (increases with debits, decreases with credits)
   - **LIABILITY accounts**: Normal balance is CREDIT (increases with credits, decreases with debits)
   - **EQUITY accounts**: Normal balance is CREDIT (increases with credits, decreases with debits)
   - **INCOME accounts**: Normal balance is CREDIT (increases with credits, decreases with debits)
   - **EXPENSE accounts**: Normal balance is DEBIT (increases with debits, decreases with credits)

3. **Common Transaction Patterns:**
   - **Salary Advances**: Debit Employee Advances (Asset), Credit Cash/Bank (Asset)
   - **Loan Disbursements**: Debit Loanbook (Asset), Credit Cash/Bank (Asset)
   - **Loan Interest**: Debit Accounts Receivable (Asset), Credit Interest Income (Income)
   - **Expense Payments**: Debit Expense Account, Credit Cash/Bank (Asset)
   - **Revenue Collection**: Debit Cash/Bank (Asset), Credit Revenue Account (Income)

## How to Create Accounting Rules

### Step 1: Access the Chart of Accounts Page
Navigate to: **Admin → Accounting → Chart of Accounts**

### Step 2: Create a New Rule
1. Click the **"+ Create Rule"** button in the Accounting Rules section
2. Fill in the form:
   - **Rule Name**: Descriptive name (e.g., "Salary Advances")
   - **Trigger Event**: Code used in your application (e.g., "salary_advance")
   - **Debit Account**: Account to be debited
   - **Credit Account**: Account to be credited
   - **Description**: Optional explanation
   - **Active**: Check to enable automatic application

### Step 3: Save the Rule
Click "Create Rule" to save. The rule will now be available for automatic application.

## Using Accounting Rules in Code

### Basic Usage

```php
use App\Services\AccountingRuleService;

// Inject the service
$accountingService = app(AccountingRuleService::class);

// Apply a rule when a transaction occurs
$journalEntry = $accountingService->applyRule(
    triggerEvent: 'salary_advance',
    amount: 5000.00,
    metadata: [
        'transaction_date' => now()->toDateString(),
        'reference_number' => 'SA-2024-001',
        'transaction_details' => 'Salary advance for John Doe',
        'description' => 'Monthly salary advance',
        'branch_name' => 'Corporate (HQ)',
        'auto_post' => true, // Automatically post the entry
    ]
);
```

### Example: Salary Advance Transaction

```php
// When processing a salary advance
public function processSalaryAdvance($employeeId, $amount)
{
    DB::beginTransaction();
    try {
        // Your business logic here (e.g., update employee record)
        
        // Automatically create journal entry using accounting rule
        $accountingService = app(AccountingRuleService::class);
        $journalEntry = $accountingService->applyRule(
            triggerEvent: 'salary_advance',
            amount: $amount,
            metadata: [
                'reference_number' => "SA-{$employeeId}-" . now()->format('Ymd'),
                'transaction_details' => "Salary advance for Employee #{$employeeId}",
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
```

### Example: Loan Disbursement

```php
public function disburseLoan($loanId, $amount)
{
    DB::beginTransaction();
    try {
        // Update loan record
        $loan = Loan::findOrFail($loanId);
        $loan->update(['status' => 'disbursed']);
        
        // Apply accounting rule
        $accountingService = app(AccountingRuleService::class);
        $journalEntry = $accountingService->applyRule(
            triggerEvent: 'loan_ledger',
            amount: $amount,
            metadata: [
                'reference_number' => "LOAN-{$loanId}",
                'transaction_details' => "Loan disbursement for Loan #{$loanId}",
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
```

## Common Trigger Events

Here are some suggested trigger events for common transactions:

| Trigger Event | Description | Typical Debit | Typical Credit |
|--------------|-------------|---------------|----------------|
| `salary_advance` | Salary advances to employees | Employee Advances | Cash/Bank |
| `loan_ledger` | Loan disbursements | Loanbook | Cash/Bank |
| `loan_overpayment` | Loan overpayments | Prepaid Loans | Loan Overpayments |
| `loan_interest` | Loan interest accrual | Accounts Receivable | Interest Income |
| `contribution` | Monthly contributions | Cash/Bank | Contributions Payable |
| `welfare_disbursement` | Welfare fund payments | Welfare Expenses | Cash/Bank |
| `investment_deposit` | Investment deposits | Investment Account | Cash/Bank |
| `expense_payment` | General expense payments | Expense Account | Cash/Bank |
| `revenue_collection` | Revenue collection | Cash/Bank | Revenue Account |

## Best Practices

### 1. Rule Naming
- Use clear, descriptive names
- Follow a consistent naming convention
- Include the transaction type in the name

### 2. Trigger Event Codes
- Use lowercase with underscores (snake_case)
- Be specific and descriptive
- Document trigger events in your codebase

### 3. Account Selection
- Always ensure debit and credit accounts are different
- Select accounts that match the transaction type
- Consider the normal balance of accounts

### 4. Testing
- Test rules with small amounts first
- Verify journal entries are created correctly
- Check that debits equal credits
- Review posted entries in the accounting module

### 5. Rule Management
- Deactivate rules instead of deleting when temporarily not needed
- Review and update rules periodically
- Document any changes to rules

## Validation Rules

The system enforces the following validations:

1. **Debit and Credit accounts must be different**
2. **Amount must be greater than zero**
3. **Accounts must be active**
4. **Rule must be active to be automatically applied**

## Troubleshooting

### Rule Not Applied
- Check if the rule is active
- Verify the trigger event code matches exactly
- Check application logs for errors
- Ensure accounts are active

### Incorrect Journal Entries
- Review the rule configuration
- Verify account selections
- Check transaction metadata
- Review posted entries for discrepancies

### Missing Journal Entries
- Confirm the rule exists and is active
- Check if the trigger event is being called
- Review application logs
- Verify database transactions are committed

## Security Considerations

1. **Access Control**: Only authorized users (Finance Admin, Treasurer, etc.) should create/edit rules
2. **Audit Trail**: All rule changes are logged in the journal entries
3. **Validation**: System validates all inputs before creating journal entries
4. **Transaction Safety**: All operations use database transactions for data integrity

## Maintenance

### Regular Tasks
1. Review active rules monthly
2. Update rules when account structure changes
3. Archive or delete unused rules
4. Monitor rule application logs

### When to Update Rules
- Account structure changes
- Business process changes
- New transaction types introduced
- Regulatory requirements change

## Support

For questions or issues:
1. Review this documentation
2. Check application logs
3. Contact the Finance Admin or Treasurer
4. Review posted journal entries for patterns

---

**Last Updated**: {{ date('Y-m-d') }}
**Version**: 1.0
