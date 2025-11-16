<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_shifts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('shift_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('break_minutes')->default(0);
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'shift_date']);
            $table->index(['branch_id', 'shift_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_shifts');
    }
};
