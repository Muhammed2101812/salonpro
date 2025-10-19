<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Service Packages
        Schema::create('service_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->integer('validity_days')->nullable(); // Package valid for X days
            $table->integer('max_uses')->nullable(); // Can be used X times
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Package Services (many-to-many)
        Schema::create('package_service', function (Blueprint $table) {
            $table->uuid('package_id');
            $table->uuid('service_id');
            $table->integer('quantity')->default(1); // How many times this service is included
            $table->decimal('price_override', 10, 2)->nullable(); // Optional custom price for this service in package
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('service_packages')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->primary(['package_id', 'service_id']);
        });

        // Price History
        Schema::create('service_price_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id');
            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->decimal('price_change', 10, 2);
            $table->decimal('price_change_percentage', 5, 2);
            $table->uuid('changed_by')->nullable(); // User who made the change
            $table->text('reason')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['service_id', 'changed_at']);
        });

        // Dynamic Pricing Rules
        Schema::create('service_pricing_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id');
            $table->string('rule_name');
            $table->enum('rule_type', ['time_based', 'day_based', 'customer_based', 'seasonal', 'demand_based'])->default('time_based');
            $table->json('conditions'); // JSON conditions for the rule
            $table->enum('adjustment_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('adjustment_value', 10, 2);
            $table->integer('priority')->default(0); // Higher priority rules apply first
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });

        // Service Templates (for quick service creation)
        Schema::create('service_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('default_data'); // Default values for creating a service
            $table->boolean('is_system')->default(false); // System templates can't be deleted
            $table->timestamps();
        });

        // Service Add-ons (optional extras)
        Schema::create('service_addons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration')->default(0); // Additional minutes
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Service - Add-on pivot
        Schema::create('service_addon', function (Blueprint $table) {
            $table->uuid('service_id');
            $table->uuid('addon_id');
            $table->decimal('price_override', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('addon_id')->references('id')->on('service_addons')->onDelete('cascade');
            $table->primary(['service_id', 'addon_id']);
        });

        // Service Requirements (products/equipment needed)
        Schema::create('service_requirements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id');
            $table->enum('requirement_type', ['product', 'equipment', 'skill', 'certification'])->default('product');
            $table->uuid('product_id')->nullable();
            $table->string('requirement_name'); // For equipment, skill, certification
            $table->decimal('quantity', 10, 2)->default(1);
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });

        // Service Reviews/Ratings
        Schema::create('service_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id');
            $table->uuid('customer_id');
            $table->uuid('appointment_id')->nullable();
            $table->integer('rating'); // 1-5
            $table->text('review')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_reviews');
        Schema::dropIfExists('service_requirements');
        Schema::dropIfExists('service_addon');
        Schema::dropIfExists('service_addons');
        Schema::dropIfExists('service_templates');
        Schema::dropIfExists('service_pricing_rules');
        Schema::dropIfExists('service_price_history');
        Schema::dropIfExists('package_service');
        Schema::dropIfExists('service_packages');
    }
};
