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
        Schema::create('branch_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, number, boolean, json, array
            $table->string('group')->nullable(); // business, appointments, notifications, financial, etc.
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            // Indexes
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['branch_id', 'key']);
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_settings');
    }
};
