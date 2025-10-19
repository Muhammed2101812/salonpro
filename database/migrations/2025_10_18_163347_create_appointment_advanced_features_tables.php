<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recurring Appointments Pattern
        Schema::create('appointment_recurrences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('service_id');
            $table->uuid('employee_id')->nullable();
            $table->uuid('branch_id');
            $table->time('preferred_time');
            $table->enum('recurrence_type', ['daily', 'weekly', 'biweekly', 'monthly'])->default('weekly');
            $table->json('recurrence_pattern'); // e.g., {"days": ["monday", "friday"], "interval": 2}
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('occurrences')->nullable(); // Total number of appointments to create
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Link recurring pattern to actual appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->uuid('recurrence_id')->nullable()->after('branch_id');
            $table->foreign('recurrence_id')->references('id')->on('appointment_recurrences')->onDelete('set null');
        });

        // Group Bookings
        Schema::create('appointment_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('group_name');
            $table->integer('max_participants');
            $table->integer('current_participants')->default(0);
            $table->uuid('service_id');
            $table->uuid('employee_id')->nullable();
            $table->dateTime('scheduled_at');
            $table->integer('duration'); // minutes
            $table->decimal('price_per_person', 10, 2);
            $table->enum('status', ['open', 'full', 'confirmed', 'completed', 'cancelled'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });

        // Group booking participants
        Schema::create('appointment_group_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('group_id');
            $table->uuid('customer_id');
            $table->uuid('appointment_id')->nullable(); // Link to individual appointment record
            $table->enum('status', ['confirmed', 'cancelled', 'completed'])->default('confirmed');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('appointment_groups')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->unique(['group_id', 'customer_id']);
        });

        // Waiting List
        Schema::create('appointment_waitlist', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('service_id');
            $table->uuid('employee_id')->nullable();
            $table->uuid('branch_id');
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time_start')->nullable();
            $table->time('preferred_time_end')->nullable();
            $table->json('preferred_days')->nullable(); // ["monday", "tuesday"]
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['waiting', 'notified', 'booked', 'cancelled', 'expired'])->default('waiting');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['branch_id', 'status', 'priority']);
        });

        // Appointment Conflicts Log
        Schema::create('appointment_conflicts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id')->nullable();
            $table->uuid('employee_id');
            $table->uuid('branch_id');
            $table->dateTime('conflict_start');
            $table->dateTime('conflict_end');
            $table->enum('conflict_type', ['time_overlap', 'employee_unavailable', 'resource_conflict', 'overbooking'])->default('time_overlap');
            $table->text('conflict_details')->nullable();
            $table->boolean('resolved')->default(false);
            $table->uuid('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Appointment Reminders
        Schema::create('appointment_reminders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id');
            $table->enum('reminder_type', ['sms', 'email', 'push', 'whatsapp'])->default('sms');
            $table->integer('hours_before'); // Send X hours before appointment
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->text('message')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->index(['status', 'scheduled_at']);
        });

        // Appointment History/Audit Trail
        Schema::create('appointment_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id');
            $table->uuid('user_id')->nullable();
            $table->enum('action', ['created', 'updated', 'cancelled', 'rescheduled', 'completed', 'no_show', 'confirmed'])->default('created');
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['appointment_id', 'created_at']);
        });

        // Appointment Cancellation Reasons
        Schema::create('appointment_cancellation_reasons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reason');
            $table->text('description')->nullable();
            $table->boolean('requires_note')->default(false);
            $table->boolean('is_customer_fault')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        // Link cancellations to reasons
        Schema::create('appointment_cancellations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id');
            $table->uuid('reason_id')->nullable();
            $table->uuid('cancelled_by')->nullable();
            $table->timestamp('cancelled_at');
            $table->text('cancellation_notes')->nullable();
            $table->boolean('refund_issued')->default(false);
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('reason_id')->references('id')->on('appointment_cancellation_reasons')->onDelete('set null');
            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_cancellations');
        Schema::dropIfExists('appointment_cancellation_reasons');
        Schema::dropIfExists('appointment_history');
        Schema::dropIfExists('appointment_reminders');
        Schema::dropIfExists('appointment_conflicts');
        Schema::dropIfExists('appointment_waitlist');
        Schema::dropIfExists('appointment_group_participants');
        Schema::dropIfExists('appointment_groups');

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['recurrence_id']);
            $table->dropColumn('recurrence_id');
        });

        Schema::dropIfExists('appointment_recurrences');
    }
};
