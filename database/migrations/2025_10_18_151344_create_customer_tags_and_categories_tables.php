<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Customer Categories
        Schema::create('customer_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('name');
            $table->string('color')->default('#3B82F6'); // Blue
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['branch_id', 'is_active']);
        });

        // Customer Tags
        Schema::create('customer_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('name');
            $table->string('color')->default('#10B981'); // Green
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['branch_id', 'name']);
        });

        // Customer - Category Pivot
        Schema::create('customer_category', function (Blueprint $table) {
            $table->uuid('customer_id');
            $table->uuid('category_id');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('customer_categories')->onDelete('cascade');
            $table->primary(['customer_id', 'category_id']);
        });

        // Customer - Tag Pivot
        Schema::create('customer_tag', function (Blueprint $table) {
            $table->uuid('customer_id');
            $table->uuid('tag_id');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('customer_tags')->onDelete('cascade');
            $table->primary(['customer_id', 'tag_id']);
        });

        // Customer Notes
        Schema::create('customer_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('user_id')->nullable(); // Who created the note
            $table->text('note');
            $table->boolean('is_important')->default(false);
            $table->boolean('is_private')->default(false); // Only visible to managers
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_notes');
        Schema::dropIfExists('customer_tag');
        Schema::dropIfExists('customer_category');
        Schema::dropIfExists('customer_tags');
        Schema::dropIfExists('customer_categories');
    }
};
