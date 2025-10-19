<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Employee Skills
        Schema::create('employee_skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->string('skill_name');
            $table->enum('proficiency', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            $table->integer('years_of_experience')->default(0);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        // Employee Certifications
        Schema::create('employee_certifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->string('certification_name');
            $table->string('issuing_organization')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('certificate_number')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        // Employee Work Schedules
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->uuid('branch_id');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['employee_id', 'day_of_week']);
        });

        // Employee Shifts
        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->uuid('branch_id');
            $table->date('shift_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['employee_id', 'shift_date']);
        });

        // Employee Performance
        Schema::create('employee_performance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->date('evaluation_date');
            $table->integer('customer_satisfaction_score')->nullable(); // 1-5
            $table->integer('punctuality_score')->nullable(); // 1-5
            $table->integer('sales_performance_score')->nullable(); // 1-5
            $table->integer('teamwork_score')->nullable(); // 1-5
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->integer('total_appointments')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['employee_id', 'evaluation_date']);
        });

        // Employee Commissions
        Schema::create('employee_commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->uuid('appointment_id')->nullable();
            $table->uuid('sale_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_rate', 5, 2); // Percentage
            $table->decimal('commission_amount', 10, 2);
            $table->date('commission_date');
            $table->boolean('is_paid')->default(false);
            $table->date('paid_date')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
            $table->index(['employee_id', 'commission_date']);
        });

        // Employee Leaves
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->enum('leave_type', ['annual', 'sick', 'unpaid', 'maternity', 'paternity', 'other'])->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('reason')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Employee Attendance
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->uuid('branch_id');
            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('total_hours')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendance');
        Schema::dropIfExists('employee_leaves');
        Schema::dropIfExists('employee_commissions');
        Schema::dropIfExists('employee_performance');
        Schema::dropIfExists('employee_shifts');
        Schema::dropIfExists('employee_schedules');
        Schema::dropIfExists('employee_certifications');
        Schema::dropIfExists('employee_skills');
    }
};
