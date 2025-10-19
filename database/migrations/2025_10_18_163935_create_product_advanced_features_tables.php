<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product Variants (e.g., different sizes, colors)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id'); // Parent product
            $table->string('variant_name'); // e.g., "500ml", "1000ml", "Blue", "Large"
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->json('attributes'); // e.g., {"size": "500ml", "color": "blue"}
            $table->decimal('price', 10, 2); // Variant-specific price
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->integer('max_stock_level')->nullable();
            $table->integer('reorder_point')->nullable();
            $table->integer('weight_grams')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // Product Images
        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('image_url');
            $table->string('thumbnail_url')->nullable();
            $table->string('title')->nullable();
            $table->text('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'sort_order']);
        });

        // Product Barcodes (for products with multiple barcodes)
        Schema::create('product_barcodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('barcode')->unique();
            $table->enum('barcode_type', ['ean13', 'ean8', 'upc', 'code128', 'code39', 'qr'])->default('ean13');
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // Product Discounts
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('branch_id')->nullable(); // null = global discount
            $table->string('discount_name');
            $table->enum('discount_type', ['percentage', 'fixed', 'buy_x_get_y', 'bundle'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // Percentage or fixed amount
            $table->integer('min_quantity')->default(1); // Minimum quantity for discount
            $table->integer('max_quantity')->nullable(); // Maximum quantity
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Cap for percentage discounts
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable(); // For happy hour type discounts
            $table->time('end_time')->nullable();
            $table->json('applicable_days')->nullable(); // ["monday", "friday"] for weekly discounts
            $table->json('conditions')->nullable(); // Additional conditions in JSON
            $table->integer('usage_limit')->nullable(); // How many times this discount can be used
            $table->integer('usage_count')->default(0); // Times used
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['product_id', 'is_active', 'start_date', 'end_date']);
        });

        // Product Supplier Prices (track prices from different suppliers)
        Schema::create('product_supplier_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('supplier_id');
            $table->string('supplier_sku')->nullable(); // SKU in supplier's system
            $table->decimal('price', 10, 2);
            $table->enum('currency', ['TRY', 'USD', 'EUR'])->default('TRY');
            $table->integer('minimum_order_quantity')->default(1);
            $table->integer('lead_time_days')->nullable(); // Delivery time
            $table->boolean('is_preferred')->default(false); // Preferred supplier for this product
            $table->date('price_valid_from')->nullable();
            $table->date('price_valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->unique(['product_id', 'supplier_id']);
        });

        // Product Categories Hierarchy (for nested categories)
        Schema::create('product_category_hierarchy', function (Blueprint $table) {
            $table->uuid('category_id');
            $table->uuid('parent_category_id')->nullable();
            $table->integer('level')->default(0); // 0 = root, 1 = child, etc.
            $table->string('path')->nullable(); // e.g., "1/5/12" for breadcrumb
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('parent_category_id')->references('id')->on('products')->onDelete('cascade');
            $table->primary(['category_id']);
        });

        // Product Attribute Definitions (define custom attributes like size, color, material)
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('attribute_name'); // e.g., "Size", "Color", "Material"
            $table->string('attribute_code')->unique(); // e.g., "size", "color", "material"
            $table->enum('attribute_type', ['text', 'number', 'select', 'multiselect', 'boolean'])->default('select');
            $table->json('options')->nullable(); // For select/multiselect: ["Small", "Medium", "Large"]
            $table->boolean('is_filterable')->default(true); // Can be used in product filtering
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Product Attribute Values (actual values for products)
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('attribute_id');
            $table->string('attribute_value'); // The actual value
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
            $table->primary(['product_id', 'attribute_id']);
        });

        // Product Price History (track all price changes)
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->decimal('old_cost', 10, 2)->nullable();
            $table->decimal('new_cost', 10, 2)->nullable();
            $table->decimal('price_change', 10, 2);
            $table->decimal('price_change_percentage', 5, 2);
            $table->uuid('changed_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['product_id', 'changed_at']);
        });

        // Product Stock Movement History (detailed stock tracking)
        Schema::create('product_stock_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('branch_id');
            $table->enum('movement_type', ['purchase', 'sale', 'adjustment', 'transfer_in', 'transfer_out', 'return', 'loss', 'production', 'consumption'])->default('sale');
            $table->integer('quantity_before');
            $table->integer('quantity_change'); // +10 or -5
            $table->integer('quantity_after');
            $table->uuid('reference_id')->nullable(); // ID of sale, purchase order, transfer, etc.
            $table->string('reference_type')->nullable(); // Model class name
            $table->uuid('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('movement_date');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['product_id', 'branch_id', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_history');
        Schema::dropIfExists('product_price_history');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_category_hierarchy');
        Schema::dropIfExists('product_supplier_prices');
        Schema::dropIfExists('product_discounts');
        Schema::dropIfExists('product_barcodes');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
    }
};
