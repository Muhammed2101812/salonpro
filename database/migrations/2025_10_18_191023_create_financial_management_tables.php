<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('invoice_number')->unique();
            $table->enum('invoice_type', ['sales', 'purchase', 'proforma', 'credit_note', 'debit_note'])->default('sales');
            $table->uuid('customer_id')->nullable(); // For sales invoices
            $table->uuid('supplier_id')->nullable(); // For purchase invoices
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2);
            $table->enum('currency', ['TRY', 'USD', 'EUR'])->default('TRY');
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->uuid('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->index(['branch_id', 'invoice_type', 'status', 'invoice_date']);
        });

        // Invoice Items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->enum('item_type', ['service', 'product', 'custom'])->default('service');
            $table->uuid('service_id')->nullable();
            $table->uuid('product_id')->nullable();
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });

        // Tax Rates
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g., "KDV %20", "VAT 20%"
            $table->string('code')->unique(); // e.g., "kdv_20"
            $table->decimal('rate', 5, 2); // 20.00 for 20%
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->timestamps();
        });

        // Bank Accounts
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable(); // null = company account
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->enum('currency', ['TRY', 'USD', 'EUR'])->default('TRY');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->enum('account_type', ['checking', 'savings', 'business', 'credit_card'])->default('checking');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Bank Transactions
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bank_account_id');
            $table->string('transaction_number')->nullable();
            $table->date('transaction_date');
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'transfer', 'fee', 'interest'])->default('deposit');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->uuid('reference_id')->nullable(); // Link to payment, invoice, etc.
            $table->string('reference_type')->nullable(); // Model class name
            $table->string('payee_payer')->nullable(); // Who sent/received
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'reconciled'])->default('completed');
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->index(['bank_account_id', 'transaction_date', 'status']);
        });

        // Cash Registers
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('register_name');
            $table->string('register_code')->unique();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('closed');
            $table->uuid('current_session_id')->nullable(); // Link to current session
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Cash Register Sessions
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cash_register_id');
            $table->uuid('opened_by');
            $table->uuid('closed_by')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_balance', 12, 2);
            $table->decimal('closing_balance', 12, 2)->nullable();
            $table->decimal('expected_closing_balance', 12, 2)->nullable(); // Based on transactions
            $table->decimal('difference', 10, 2)->nullable(); // Variance
            $table->decimal('total_cash_in', 12, 2)->default(0);
            $table->decimal('total_cash_out', 12, 2)->default(0);
            $table->integer('transaction_count')->default(0);
            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();
            $table->enum('status', ['open', 'closed', 'reconciled'])->default('open');
            $table->timestamps();

            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');
            $table->foreign('opened_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['cash_register_id', 'status', 'opened_at']);
        });

        // Cash Register Transactions
        Schema::create('cash_register_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id');
            $table->enum('transaction_type', ['sale', 'refund', 'cash_in', 'cash_out', 'opening', 'closing'])->default('sale');
            $table->decimal('amount', 10, 2);
            $table->uuid('reference_id')->nullable(); // Link to sale, payment, etc.
            $table->string('reference_type')->nullable();
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'check', 'voucher', 'other'])->default('cash');
            $table->text('description')->nullable();
            $table->uuid('user_id');
            $table->timestamp('transaction_time');
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('cash_register_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        // Chart of Accounts
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('account_code')->unique(); // e.g., "1000", "4100"
            $table->string('account_name');
            $table->enum('account_type', [
                'asset',
                'liability',
                'equity',
                'revenue',
                'expense',
                'cost_of_sales'
            ])->default('asset');
            $table->enum('account_subtype', [
                'current_asset',
                'fixed_asset',
                'current_liability',
                'long_term_liability',
                'owner_equity',
                'operating_revenue',
                'non_operating_revenue',
                'operating_expense',
                'non_operating_expense',
                'direct_cost'
            ])->nullable();
            $table->uuid('parent_account_id')->nullable(); // For sub-accounts
            $table->integer('level')->default(0); // 0 = main, 1 = sub, etc.
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_system_account')->default(false); // Can't be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });

        // Journal Entries
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable(); // null = company-wide
            $table->string('entry_number')->unique();
            $table->date('entry_date');
            $table->enum('entry_type', ['manual', 'system', 'adjustment', 'closing'])->default('manual');
            $table->uuid('reference_id')->nullable(); // Link to invoice, payment, etc.
            $table->string('reference_type')->nullable();
            $table->text('description');
            $table->decimal('total_debit', 12, 2);
            $table->decimal('total_credit', 12, 2);
            $table->enum('status', ['draft', 'posted', 'voided'])->default('draft');
            $table->uuid('created_by');
            $table->uuid('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['branch_id', 'entry_date', 'status']);
        });

        // Journal Entry Lines
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_entry_id');
            $table->uuid('account_id');
            $table->enum('type', ['debit', 'credit'])->default('debit');
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');
        });

        // Budget Plans
        Schema::create('budget_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable(); // null = company-wide
            $table->string('budget_name');
            $table->enum('budget_period', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->integer('fiscal_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_budget', 12, 2);
            $table->decimal('total_actual', 12, 2)->default(0);
            $table->decimal('variance', 12, 2)->default(0);
            $table->decimal('variance_percentage', 5, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'active', 'closed'])->default('draft');
            $table->uuid('created_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Budget Items
        Schema::create('budget_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('budget_plan_id');
            $table->uuid('account_id'); // Link to chart of accounts
            $table->string('category_name');
            $table->decimal('budgeted_amount', 12, 2);
            $table->decimal('actual_amount', 12, 2)->default(0);
            $table->decimal('variance', 12, 2)->default(0);
            $table->decimal('variance_percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('budget_plan_id')->references('id')->on('budget_plans')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budget_plans');
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
        Schema::dropIfExists('cash_register_transactions');
        Schema::dropIfExists('cash_register_sessions');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
