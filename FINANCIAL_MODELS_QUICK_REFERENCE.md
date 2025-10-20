# Financial Management Models - Quick Reference

## All Models Created (13 Total)

| # | Model Name | Lines of Code | Primary Purpose | Key Features |
|---|------------|---------------|-----------------|--------------|
| 1 | **Invoice** | 164 | Sales/Purchase invoicing | Multi-type, multi-currency, payment tracking |
| 2 | **InvoiceItem** | 101 | Invoice line items | Auto-calculation, tax & discount |
| 3 | **TaxRate** | 100 | Tax rate management | KDV rates, effective dates, auto-default |
| 4 | **BankAccount** | 151 | Bank account management | Deposits, withdrawals, transfers, reconciliation |
| 5 | **BankTransaction** | 154 | Bank transactions | Balance tracking, polymorphic refs |
| 6 | **CashRegister** | 140 | Cash register management | Session tracking, open/close |
| 7 | **CashRegisterSession** | 179 | Register sessions | Variance tracking, reconciliation |
| 8 | **CashRegisterTransaction** | 99 | Register transactions | Cash in/out, payment methods |
| 9 | **ChartOfAccount** | 188 | Account hierarchy | Double-entry, parent/child structure |
| 10 | **JournalEntry** | 203 | Journal entries | Post/void, balanced entries |
| 11 | **JournalEntryLine** | 87 | Entry line items | Debit/credit, auto-totals |
| 12 | **BudgetPlan** | 201 | Budget planning | Variance tracking, approval workflow |
| 13 | **BudgetItem** | 146 | Budget line items | Utilization %, on-track checking |

**Total Lines of Code**: 1,913

---

## Model Categories

### 1. Invoicing System (3 models)
- **Invoice**: Main invoice management
- **InvoiceItem**: Invoice line items
- **TaxRate**: Tax rates configuration

### 2. Banking System (2 models)
- **BankAccount**: Bank account management
- **BankTransaction**: Transaction history

### 3. Cash Register System (3 models)
- **CashRegister**: Register configuration
- **CashRegisterSession**: Daily sessions
- **CashRegisterTransaction**: Individual transactions

### 4. Accounting System (3 models)
- **ChartOfAccount**: Account structure
- **JournalEntry**: Journal entries
- **JournalEntryLine**: Entry details

### 5. Budget System (2 models)
- **BudgetPlan**: Budget planning
- **BudgetItem**: Budget details

---

## Common Operations Cheat Sheet

### Invoice Operations
```php
// Create invoice
$invoice = Invoice::create([...]);

// Add items
$invoice->items()->create([...]);

// Record payment
$invoice->recordPayment(100.00);

// Check status
if ($invoice->isOverdue()) { ... }
```

### Bank Account Operations
```php
// Deposit money
$account->deposit(1000, ['description' => 'Sale payment']);

// Withdraw money
$account->withdraw(500, ['description' => 'Expense payment']);

// Transfer between accounts
$fromAccount->transfer($toAccount, 250, ['description' => 'Transfer']);

// Reconcile
$account->reconcile();
```

### Cash Register Operations
```php
// Open session
$session = $register->openSession($user, 100.00, 'Opening notes');

// Add transaction
$register->addTransaction([
    'transaction_type' => 'sale',
    'amount' => 50.00,
]);

// Close session
$register->closeCurrentSession($user, 150.00, 'Closing notes');
```

### Journal Entry Operations
```php
// Create entry
$entry = JournalEntry::create([...]);

// Add lines
$entry->addLine([
    'account_id' => $accountId,
    'type' => 'debit',
    'amount' => 100.00,
]);

// Post entry
$entry->post($user);

// Check balance
if ($entry->isBalanced()) { ... }
```

### Budget Operations
```php
// Create budget
$budget = BudgetPlan::create([...]);

// Add items
$budget->items()->create([...]);

// Approve
$budget->approve($user);

// Activate
$budget->activate();

// Check status
if ($budget->isOverBudget()) { ... }
```

---

## Key Relationships

### Primary Relationships
```
Branch (1) -> (N) Invoice
Branch (1) -> (N) BankAccount
Branch (1) -> (N) CashRegister
Branch (1) -> (N) JournalEntry
Branch (1) -> (N) BudgetPlan

Invoice (1) -> (N) InvoiceItem
BankAccount (1) -> (N) BankTransaction
CashRegister (1) -> (N) CashRegisterSession
CashRegisterSession (1) -> (N) CashRegisterTransaction
JournalEntry (1) -> (N) JournalEntryLine
BudgetPlan (1) -> (N) BudgetItem

ChartOfAccount (1) -> (N) ChartOfAccount (self-referential)
ChartOfAccount (1) -> (N) JournalEntryLine
ChartOfAccount (1) -> (N) BudgetItem
```

### Polymorphic Relationships
```
BankTransaction -> morphTo -> (Invoice, Payment, etc.)
CashRegisterTransaction -> morphTo -> (Sale, Payment, etc.)
JournalEntry -> morphTo -> (Invoice, Payment, etc.)
```

---

## Status Workflows

### Invoice Status Flow
```
draft -> sent -> paid
             -> partial -> paid
             -> overdue -> paid
             -> cancelled
```

### Bank Transaction Status Flow
```
pending -> completed
        -> cancelled
        -> reconciled
```

### Cash Register Status Flow
```
closed -> open -> closed
```

### Cash Register Session Status Flow
```
open -> closed -> reconciled
```

### Journal Entry Status Flow
```
draft -> posted
      -> voided
```

### Budget Plan Status Flow
```
draft -> approved -> active -> closed
```

---

## Scope Usage Examples

```php
// Active records
$accounts = BankAccount::active()->get();
$registers = CashRegister::active()->get();

// By branch
$invoices = Invoice::forBranch($branchId)->get();
$accounts = BankAccount::forBranch($branchId)->get();

// By status
$unpaid = Invoice::unpaid()->get();
$overdue = Invoice::overdue()->get();
$posted = JournalEntry::posted()->get();

// By type
$assets = ChartOfAccount::byType('asset')->get();
$deposits = BankTransaction::deposits()->get();

// Date ranges
$transactions = BankTransaction::inDateRange($start, $end)->get();
$entries = JournalEntry::inDateRange($start, $end)->get();

// Budget specific
$activeBudgets = BudgetPlan::active()->get();
$overBudget = BudgetPlan::overBudget()->get();
$overItems = BudgetItem::overBudget()->get();
```

---

## Important Helper Methods

### Financial Calculations
- `Invoice::calculateBalanceDue()`: Remaining amount
- `InvoiceItem::calculateTotal()`: Line total with tax/discount
- `TaxRate::calculateTaxAmount($amount)`: Tax calculation
- `BankAccount::calculateBalance()`: Account balance
- `ChartOfAccount::debit($amount)` / `credit($amount)`: Post to account
- `BudgetItem::getUtilizationPercentage()`: Budget usage %

### Status Checks
- `Invoice::isOverdue()`, `isPaid()`, `isPartiallyPaid()`
- `CashRegister::isOpen()`, `isClosed()`
- `JournalEntry::isBalanced()`, `isDraft()`, `isPosted()`
- `BudgetPlan::isOverBudget()`, `isUnderBudget()`
- `BudgetItem::isOnTrack()`: Check spending pace

### Actions
- `Invoice::recordPayment($amount)`: Record payment
- `BankAccount::deposit($amount)`, `withdraw($amount)`, `transfer($to, $amount)`
- `CashRegister::openSession()`, `closeCurrentSession()`
- `JournalEntry::post($user)`, `void($user)`
- `BudgetPlan::approve($user)`, `activate()`, `close()`

---

## Auto-Generated Fields

| Model | Field | Format | Example |
|-------|-------|--------|---------|
| BankTransaction | transaction_number | BT-YYYYMMDD-#### | BT-20251019-0001 |
| JournalEntry | entry_number | JE-YYYYMMDD-#### | JE-20251019-0001 |

---

## Soft Deletes

Models with soft deletes:
- Invoice
- BankAccount

All other models use hard deletes.

---

## Turkish Error Messages

All models include Turkish error messages:
- "Yetersiz bakiye" (Insufficient balance)
- "Kasa zaten açık" (Register already open)
- "Oturum açık değil" (Session not open)
- "Bu hesap silinemez" (This account cannot be deleted)
- "Yevmiye kaydı dengeli değil" (Journal entry not balanced)

---

## Validation Rules

### Built-in Validations
1. **BankAccount**: Balance check before withdrawal
2. **JournalEntry**: Debits must equal credits
3. **ChartOfAccount**: System accounts cannot be deleted
4. **Invoice**: Payment cannot exceed total
5. **BudgetPlan**: Only approved budgets can be activated

---

## File Locations

All models located at:
```
C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\
```

Files created:
1. Invoice.php
2. InvoiceItem.php
3. TaxRate.php
4. BankAccount.php
5. BankTransaction.php
6. CashRegister.php
7. CashRegisterSession.php
8. CashRegisterTransaction.php
9. ChartOfAccount.php
10. JournalEntry.php
11. JournalEntryLine.php
12. BudgetPlan.php
13. BudgetItem.php

---

## Dependencies

All models depend on these existing models:
- Branch
- Customer
- Supplier
- User
- Service
- Product
- Payment

All dependencies verified and exist.

---

## Syntax Validation

All models validated with `php -l`:
- No syntax errors detected
- All models ready to use
- Total: 1,913 lines of code

---

## Next Implementation Steps

1. **Controllers**: Create API controllers for each model
2. **Requests**: Create validation request classes
3. **Resources**: Create API resource transformers
4. **Services**: Create service classes for business logic
5. **Policies**: Create authorization policies
6. **Seeders**: Create seeders for tax rates and chart of accounts
7. **Tests**: Create unit and feature tests
8. **Routes**: Add API routes
9. **Documentation**: Create API documentation

---

## Additional Features Implemented

- UUID primary keys on all models
- Comprehensive relationships (BelongsTo, HasMany, MorphTo, MorphMany)
- Query scopes for common filters
- Helper methods for business logic
- Proper type casting (decimal:2, date, datetime, boolean)
- Auto-calculation methods
- Status workflow management
- Turkish language support
- Validation and error handling
- Auto-numbering where appropriate
- Soft deletes where appropriate
