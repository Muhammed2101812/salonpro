<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Turkey');
            $table->string('tax_number')->nullable();
            $table->string('tax_office')->nullable();
            $table->json('contact_person')->nullable(); // {name, phone, email}
            $table->enum('payment_terms', ['cash', 'net_15', 'net_30', 'net_60', 'net_90'])->default('net_30');
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->integer('rating')->nullable(); // 1-5 stars
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->uuid('supplier_id');
            $table->string('po_number')->unique(); // Auto-generated PO number
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'confirmed', 'partial', 'received', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->uuid('created_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['branch_id', 'status', 'order_date']);
        });

        // Purchase Order Items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_order_id');
            $table->uuid('product_id');
            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

        // Stock Alerts
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('branch_id');
            $table->enum('alert_type', ['low_stock', 'out_of_stock', 'overstock', 'expiring_soon', 'expired'])->default('low_stock');
            $table->integer('current_quantity');
            $table->integer('threshold_quantity')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['active', 'acknowledged', 'resolved', 'ignored'])->default('active');
            $table->uuid('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('acknowledged_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['branch_id', 'status', 'severity']);
        });

        // Stock Transfers
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transfer_number')->unique();
            $table->uuid('from_branch_id');
            $table->uuid('to_branch_id');
            $table->uuid('product_id');
            $table->integer('quantity');
            $table->enum('status', ['pending', 'in_transit', 'received', 'rejected', 'cancelled'])->default('pending');
            $table->date('transfer_date');
            $table->date('expected_arrival_date')->nullable();
            $table->date('actual_arrival_date')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->uuid('requested_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->uuid('received_by')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('from_branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('to_branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['from_branch_id', 'to_branch_id', 'status']);
        });

        // Stock Audits
        Schema::create('stock_audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('audit_number')->unique();
            $table->date('audit_date');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('audit_type', ['full', 'partial', 'cycle_count', 'spot_check'])->default('full');
            $table->integer('total_products_counted')->default(0);
            $table->integer('discrepancies_found')->default(0);
            $table->decimal('total_value_adjustment', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->uuid('conducted_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('conducted_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Stock Audit Items
        Schema::create('stock_audit_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stock_audit_id');
            $table->uuid('product_id');
            $table->integer('system_quantity'); // Quantity in system
            $table->integer('actual_quantity'); // Counted quantity
            $table->integer('difference'); // actual - system
            $table->enum('variance_type', ['match', 'shortage', 'overage'])->default('match');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('value_adjustment', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('stock_audit_id')->references('id')->on('stock_audits')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

        // Product Bundles/Kits
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->decimal('bundle_price', 10, 2);
            $table->decimal('original_total_price', 10, 2); // Sum of individual prices
            $table->decimal('discount_amount', 10, 2); // Savings
            $table->decimal('discount_percentage', 5, 2);
            $table->integer('quantity_available')->default(0); // Based on component availability
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('image_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Product Bundle Items (components of bundle)
        Schema::create('product_bundle_items', function (Blueprint $table) {
            $table->uuid('bundle_id');
            $table->uuid('product_id');
            $table->integer('quantity')->default(1); // How many of this product in the bundle
            $table->decimal('individual_price', 10, 2); // Price of this item if sold separately
            $table->timestamps();

            $table->foreign('bundle_id')->references('id')->on('product_bundles')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->primary(['bundle_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bundle_items');
        Schema::dropIfExists('product_bundles');
        Schema::dropIfExists('stock_audit_items');
        Schema::dropIfExists('stock_audits');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_alerts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
    }
};
