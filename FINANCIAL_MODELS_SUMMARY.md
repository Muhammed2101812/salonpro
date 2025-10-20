# Financial Management Models Summary

## Overview
Created 13 comprehensive Financial Management models based on the migration file `database/migrations/2025_10_18_191023_create_financial_management_tables.php`.

All models include:
- HasFactory, HasUuids traits
- SoftDeletes (where applicable)
- Proper fillable arrays
- Type casting for decimals, dates, and booleans
- Complete relationships (BelongsTo, HasMany, MorphTo, MorphMany)
- Helper methods for business logic
- Query scopes for common filters
- Turkish error messages

---

## Models Created

### 1. Invoice
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\Invoice.php`

**Purpose**: Manages sales, purchase, proforma, credit notes, and debit notes

**Key Features**:
- Multiple invoice types (sales, purchase, proforma, credit_note, debit_note)
- Status tracking (draft, sent, paid, partial, overdue, cancelled)
- Multi-currency support (TRY, USD, EUR)
- Automatic balance calculations
- Payment recording

**Relationships**:
- belongsTo: Branch, Customer, Supplier, User (creator)
- hasMany: InvoiceItem
- morphMany: Payment, JournalEntry

**Helper Methods**:
- `isOverdue()`: Check if invoice is overdue
- `isPaid()`: Check if fully paid
- `isPartiallyPaid()`: Check if partially paid
- `recordPayment($amount)`: Record payment and update status
- `markAsOverdue()`: Mark invoice as overdue
- `calculateBalanceDue()`: Calculate remaining balance

**Scopes**:
- `forBranch($branchId)`: Filter by branch
- `byType($type)`: Filter by invoice type
- `byStatus($status)`: Filter by status
- `overdue()`: Get overdue invoices
- `unpaid()`: Get unpaid invoices

---

### 2. InvoiceItem
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\InvoiceItem.php`

**Purpose**: Line items for invoices

**Key Features**:
- Item types (service, product, custom)
- Quantity-based calculations
- Tax and discount handling
- Automatic total calculation

**Relationships**:
- belongsTo: Invoice, Service, Product

**Helper Methods**:
- `calculateSubtotal()`: Calculate quantity × unit price
- `calculateDiscountAmount()`: Calculate discount
- `calculateTaxAmount()`: Calculate tax
- `calculateTotal()`: Calculate final total
- `recalculate()`: Recalculate all amounts
- `getItemAttribute()`: Get related service or product

---

### 3. TaxRate
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\TaxRate.php`

**Purpose**: Manage tax rates (KDV, VAT, etc.)

**Key Features**:
- Unique code system (e.g., "kdv_20")
- Default tax rate designation
- Effective date ranges
- Active/inactive status

**Helper Methods**:
- `isEffective($date)`: Check if rate is effective on date
- `calculateTaxAmount($amount)`: Calculate tax
- `getAmountWithTax($amount)`: Add tax to amount
- `getAmountWithoutTax($amountWithTax)`: Calculate base amount

**Scopes**:
- `active()`: Active rates only
- `default()`: Default tax rate
- `effectiveOn($date)`: Effective on specific date

**Special Behavior**:
- Auto-unsets other defaults when setting new default

---

### 4. BankAccount
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\BankAccount.php`

**Purpose**: Manage bank accounts for branches or company

**Key Features**:
- Multiple account types (checking, savings, business, credit_card)
- Multi-currency support
- Opening and current balance tracking
- IBAN and SWIFT code support
- Soft deletes

**Relationships**:
- belongsTo: Branch (nullable for company accounts)
- hasMany: BankTransaction

**Helper Methods**:
- `deposit($amount, $data)`: Add funds and create transaction
- `withdraw($amount, $data)`: Remove funds with validation
- `transfer($toAccount, $amount, $data)`: Transfer between accounts
- `calculateBalance()`: Calculate balance from transactions
- `reconcile()`: Sync current balance with calculated

**Scopes**:
- `active()`: Active accounts only
- `forBranch($branchId)`: Branch accounts
- `companyAccounts()`: Company-wide accounts
- `byCurrency($currency)`: Filter by currency

---

### 5. BankTransaction
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\BankTransaction.php`

**Purpose**: Record all bank account transactions

**Key Features**:
- Transaction types (deposit, withdrawal, transfer, fee, interest)
- Status tracking (pending, completed, cancelled, reconciled)
- Balance before/after tracking
- Polymorphic reference to source

**Relationships**:
- belongsTo: BankAccount, User (creator)
- morphTo: reference (Invoice, Payment, etc.)

**Helper Methods**:
- `cancel()`: Reverse transaction
- `reconcile()`: Mark as reconciled
- `isDeposit()`: Check if deposit type
- `isWithdrawal()`: Check if withdrawal type

**Scopes**:
- `forAccount($accountId)`: Filter by account
- `byType($type)`, `byStatus($status)`
- `completed()`, `pending()`
- `deposits()`, `withdrawals()`
- `inDateRange($start, $end)`

**Auto-generation**:
- Transaction number: BT-YYYYMMDD-0001

---

### 6. CashRegister
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\CashRegister.php`

**Purpose**: Physical cash registers at branches

**Key Features**:
- Register code system
- Open/closed status
- Current session tracking
- Balance management

**Relationships**:
- belongsTo: Branch, CashRegisterSession (current)
- hasMany: CashRegisterSession

**Helper Methods**:
- `openSession($user, $balance, $notes)`: Open new session
- `closeCurrentSession($user, $balance, $notes)`: Close session
- `addTransaction($data)`: Add transaction to current session
- `isOpen()`, `isClosed()`: Status checks

**Scopes**:
- `active()`, `open()`, `closed()`
- `forBranch($branchId)`

---

### 7. CashRegisterSession
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\CashRegisterSession.php`

**Purpose**: Cash register operating sessions

**Key Features**:
- Opening/closing balance tracking
- Expected vs actual balance
- Variance calculation
- Transaction count and totals
- Status tracking (open, closed, reconciled)

**Relationships**:
- belongsTo: CashRegister, User (opened_by, closed_by)
- hasMany: CashRegisterTransaction

**Helper Methods**:
- `close($user, $balance, $notes)`: Close session
- `reconcile()`: Mark as reconciled
- `addTransaction($data)`: Add transaction
- `calculateExpectedBalance()`: Calculate expected balance
- `getDuration()`: Get session duration
- `hasVariance()`: Check if variance exists

**Scopes**:
- `open()`, `closed()`, `reconciled()`
- `forRegister($registerId)`
- `inDateRange($start, $end)`

---

### 8. CashRegisterTransaction
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\CashRegisterTransaction.php`

**Purpose**: Individual cash register transactions

**Key Features**:
- Transaction types (sale, refund, cash_in, cash_out, opening, closing)
- Payment method tracking
- Polymorphic reference to source

**Relationships**:
- belongsTo: CashRegisterSession, User
- morphTo: reference (Sale, Payment, etc.)

**Helper Methods**:
- `isCashIn()`: Check if cash in type
- `isCashOut()`: Check if cash out type
- `getSignedAmount()`: Get amount with sign

**Scopes**:
- `forSession($sessionId)`
- `byType($type)`, `byPaymentMethod($method)`
- `cashIn()`, `cashOut()`
- `sales()`, `refunds()`

---

### 9. ChartOfAccount
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\ChartOfAccount.php`

**Purpose**: Chart of accounts for accounting system

**Key Features**:
- Account types (asset, liability, equity, revenue, expense, cost_of_sales)
- Subtypes for detailed classification
- Hierarchical structure (parent/child)
- Level tracking
- System account protection

**Relationships**:
- belongsTo: ChartOfAccount (parent)
- hasMany: ChartOfAccount (sub-accounts), JournalEntryLine, BudgetItem

**Helper Methods**:
- `isAsset()`, `isLiability()`, `isEquity()`, `isRevenue()`, `isExpense()`
- `debit($amount)`: Post debit transaction
- `credit($amount)`: Post credit transaction
- `getFullAccountCode()`: Get hierarchical code
- `getFullAccountName()`: Get hierarchical name
- `hasSubAccounts()`: Check for children
- `canBeDeleted()`: Validate deletion

**Scopes**:
- `active()`
- `byType($type)`, `bySubtype($subtype)`
- `mainAccounts()`, `subAccounts()`
- `systemAccounts()`

**Protection**:
- Cannot delete system accounts or accounts with sub-accounts/transactions

---

### 10. JournalEntry
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\JournalEntry.php`

**Purpose**: Double-entry journal entries

**Key Features**:
- Entry types (manual, system, adjustment, closing)
- Status workflow (draft, posted, voided)
- Balanced entry validation
- Polymorphic reference to source

**Relationships**:
- belongsTo: Branch, User (creator, poster)
- hasMany: JournalEntryLine
- morphTo: reference (Invoice, Payment, etc.)

**Helper Methods**:
- `post($user)`: Post entry and update account balances
- `void($user)`: Void entry and reverse balances
- `addLine($data)`: Add line item
- `recalculateTotals()`: Update debit/credit totals
- `isBalanced()`: Check if debits = credits
- `isDraft()`, `isPosted()`, `isVoided()`

**Scopes**:
- `draft()`, `posted()`, `voided()`
- `forBranch($branchId)`
- `byType($type)`
- `inDateRange($start, $end)`

**Auto-generation**:
- Entry number: JE-YYYYMMDD-0001

---

### 11. JournalEntryLine
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\JournalEntryLine.php`

**Purpose**: Line items for journal entries

**Key Features**:
- Debit/credit type
- Account linking
- Auto-recalculation of parent totals

**Relationships**:
- belongsTo: JournalEntry, ChartOfAccount

**Helper Methods**:
- `isDebit()`, `isCredit()`
- `getSignedAmount()`: Amount with sign

**Scopes**:
- `forEntry($entryId)`, `forAccount($accountId)`
- `debits()`, `credits()`

**Auto-behavior**:
- Updates parent JournalEntry totals on save/delete

---

### 12. BudgetPlan
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\BudgetPlan.php`

**Purpose**: Budget planning and tracking

**Key Features**:
- Budget periods (monthly, quarterly, yearly)
- Fiscal year tracking
- Status workflow (draft, approved, active, closed)
- Variance tracking (amount and percentage)
- Approval workflow

**Relationships**:
- belongsTo: Branch, User (creator, approver)
- hasMany: BudgetItem

**Helper Methods**:
- `approve($user)`: Approve budget
- `activate()`: Activate approved budget
- `close()`: Close and finalize budget
- `recalculateAll()`: Update all items and totals
- `getUtilizationPercentage()`: Calculate % used
- `isOverBudget()`, `isUnderBudget()`
- `isDraft()`, `isApproved()`, `isActive()`, `isClosed()`

**Scopes**:
- `draft()`, `approved()`, `active()`, `closed()`
- `forBranch($branchId)`
- `forFiscalYear($year)`
- `byPeriod($period)`
- `overBudget()`

---

### 13. BudgetItem
**File**: `C:\Users\muham\Desktop\code\Projects\SalonManager\app\Models\BudgetItem.php`

**Purpose**: Individual budget line items

**Key Features**:
- Account linking
- Budgeted vs actual tracking
- Variance calculation
- Performance indicators

**Relationships**:
- belongsTo: BudgetPlan, ChartOfAccount

**Helper Methods**:
- `recalculate()`: Update actual from journal entries
- `getUtilizationPercentage()`: Calculate % used
- `getRemainingBudget()`: Calculate remaining
- `isOverBudget()`, `isUnderBudget()`
- `isOnTrack()`: Check if spending pace is appropriate

**Scopes**:
- `forBudget($budgetId)`, `forAccount($accountId)`
- `overBudget()`, `underBudget()`

**Auto-behavior**:
- Updates parent BudgetPlan totals on save/delete

---

## Model Relationships Overview

### Invoice System
```
Invoice (1) -> (N) InvoiceItem
Invoice (N) -> (1) Customer
Invoice (N) -> (1) Supplier
Invoice (N) -> (1) Branch
Invoice (N) -> (1) User (creator)
Invoice (1) -> (N) Payment (polymorphic)
Invoice (1) -> (N) JournalEntry (polymorphic)
InvoiceItem (N) -> (1) Service
InvoiceItem (N) -> (1) Product
```

### Banking System
```
BankAccount (1) -> (N) BankTransaction
BankAccount (N) -> (1) Branch
BankTransaction (N) -> (1) User (creator)
BankTransaction (N) -> (1) * (polymorphic reference)
```

### Cash Register System
```
CashRegister (1) -> (N) CashRegisterSession
CashRegister (N) -> (1) Branch
CashRegister (N) -> (1) CashRegisterSession (current)
CashRegisterSession (1) -> (N) CashRegisterTransaction
CashRegisterSession (N) -> (1) User (opened_by)
CashRegisterSession (N) -> (1) User (closed_by)
CashRegisterTransaction (N) -> (1) User
CashRegisterTransaction (N) -> (1) * (polymorphic reference)
```

### Accounting System
```
ChartOfAccount (1) -> (N) ChartOfAccount (parent-child)
ChartOfAccount (1) -> (N) JournalEntryLine
ChartOfAccount (1) -> (N) BudgetItem
JournalEntry (1) -> (N) JournalEntryLine
JournalEntry (N) -> (1) Branch
JournalEntry (N) -> (1) User (creator)
JournalEntry (N) -> (1) User (poster)
JournalEntry (N) -> (1) * (polymorphic reference)
JournalEntryLine (N) -> (1) ChartOfAccount
```

### Budget System
```
BudgetPlan (1) -> (N) BudgetItem
BudgetPlan (N) -> (1) Branch
BudgetPlan (N) -> (1) User (creator)
BudgetPlan (N) -> (1) User (approver)
BudgetItem (N) -> (1) ChartOfAccount
```

---

## Common Patterns Used

### 1. UUID Primary Keys
All models use `HasUuids` trait for UUID primary keys.

### 2. Soft Deletes
Models with soft deletes:
- Invoice
- BankAccount

### 3. Polymorphic Relationships
Models using polymorphic relations:
- BankTransaction (reference)
- CashRegisterTransaction (reference)
- JournalEntry (reference)
- Invoice (payments via Payment model)

### 4. Auto-numbering
Models with automatic number generation:
- BankTransaction: BT-YYYYMMDD-0001
- JournalEntry: JE-YYYYMMDD-0001

### 5. Balance Tracking
Models tracking balances:
- BankAccount
- CashRegister
- CashRegisterSession
- ChartOfAccount

### 6. Status Workflows
Models with status management:
- Invoice: draft → sent → paid/partial/overdue/cancelled
- BankTransaction: pending → completed/cancelled/reconciled
- CashRegister: open ↔ closed
- CashRegisterSession: open → closed → reconciled
- JournalEntry: draft → posted/voided
- BudgetPlan: draft → approved → active → closed

### 7. Calculation Methods
All financial models include:
- Amount calculation methods
- Balance tracking methods
- Variance/difference calculations
- Percentage calculations

### 8. Validation
Built-in validations:
- Balance checks before withdrawal
- Balanced entry validation (debits = credits)
- Status transition validation
- Deletion protection for system records

---

## Turkish Language Support
All models include Turkish error messages for business rules:
- "Yetersiz bakiye" (Insufficient balance)
- "Kasa zaten açık" (Register already open)
- "Oturum açık değil" (Session not open)
- "Bu hesap silinemez" (This account cannot be deleted)

---

## Next Steps

### 1. Create Related Seeder
Consider creating a seeder for:
- Default tax rates (KDV 1%, 8%, 18%)
- Chart of accounts (standard Turkish accounting plan)
- Initial cash registers

### 2. Create Controllers
- InvoiceController
- BankAccountController
- CashRegisterController
- JournalEntryController
- BudgetPlanController

### 3. Create Form Requests
- StoreInvoiceRequest / UpdateInvoiceRequest
- StoreBankAccountRequest / UpdateBankAccountRequest
- etc.

### 4. Create API Resources
- InvoiceResource
- BankAccountResource
- CashRegisterResource
- JournalEntryResource
- BudgetPlanResource

### 5. Create Services
- InvoiceService (for invoice generation logic)
- BankingService (for banking operations)
- CashRegisterService (for register management)
- AccountingService (for journal entries)
- BudgetService (for budget management)

### 6. Create Policies
Authorization policies for each model

### 7. Create Tests
- Unit tests for calculations
- Feature tests for API endpoints
- Integration tests for workflows

---

## Notes

- All models follow Laravel best practices
- All decimal fields use 'decimal:2' casting
- All date fields use appropriate casting (date/datetime)
- All boolean fields use boolean casting
- All relationships are properly defined
- All models include helpful business logic methods
- All models include query scopes for common filters
- Turkish language support throughout
- Proper use of traits (HasFactory, HasUuids, SoftDeletes)
- Comprehensive documentation in code comments
