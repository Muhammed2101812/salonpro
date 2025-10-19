<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Report Templates
        Schema::create('report_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('template_name');
            $table->string('template_code')->unique();
            $table->text('description')->nullable();
            $table->enum('category', [
                'financial',
                'sales',
                'inventory',
                'customer',
                'employee',
                'appointment',
                'marketing',
                'custom'
            ])->default('custom');
            $table->json('parameters')->nullable(); // Report parameters/filters
            $table->json('columns')->nullable(); // Column definitions
            $table->text('query')->nullable(); // SQL query or query builder config
            $table->enum('output_format', ['pdf', 'excel', 'csv', 'html'])->default('pdf');
            $table->string('template_file')->nullable(); // Blade template or PDF template
            $table->boolean('is_system')->default(false); // System templates can't be deleted
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Report Schedules
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('template_id');
            $table->uuid('branch_id')->nullable();
            $table->string('schedule_name');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->json('schedule_config'); // {day_of_week, day_of_month, time, etc.}
            $table->json('parameters')->nullable(); // Report parameters
            $table->json('recipients'); // Email addresses to send report
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('report_templates')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Report Executions (history)
        Schema::create('report_executions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('template_id')->nullable();
            $table->uuid('schedule_id')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->json('parameters')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('execution_time_ms')->nullable(); // Milliseconds
            $table->integer('row_count')->nullable();
            $table->string('output_file')->nullable(); // Generated file path
            $table->enum('output_format', ['pdf', 'excel', 'csv', 'html'])->default('pdf');
            $table->bigInteger('file_size')->nullable(); // Bytes
            $table->text('error_message')->nullable();
            $table->uuid('executed_by');
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('report_templates')->onDelete('set null');
            $table->foreign('schedule_id')->references('id')->on('report_schedules')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('executed_by')->references('id')->on('users')->onDelete('restrict');
            $table->index(['template_id', 'status', 'created_at']);
        });

        // KPI Definitions
        Schema::create('kpi_definitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kpi_code')->unique();
            $table->string('kpi_name');
            $table->text('description')->nullable();
            $table->enum('category', [
                'financial',
                'sales',
                'customer',
                'employee',
                'operational',
                'marketing'
            ])->default('operational');
            $table->enum('calculation_method', ['sum', 'average', 'count', 'min', 'max', 'custom'])->default('sum');
            $table->text('calculation_formula')->nullable(); // For custom calculations
            $table->string('unit')->nullable(); // e.g., 'TRY', '%', 'count'
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('warning_threshold', 15, 2)->nullable();
            $table->decimal('critical_threshold', 15, 2)->nullable();
            $table->boolean('higher_is_better')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // KPI Values (actual measurements)
        Schema::create('kpi_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kpi_definition_id');
            $table->uuid('branch_id')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('actual_value', 15, 2);
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('variance', 15, 2)->nullable();
            $table->decimal('variance_percentage', 5, 2)->nullable();
            $table->enum('performance_status', ['excellent', 'good', 'warning', 'critical'])->default('good');
            $table->text('notes')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->foreign('kpi_definition_id')->references('id')->on('kpi_definitions')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['kpi_definition_id', 'branch_id', 'period_start']);
        });

        // Dashboard Widgets
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('widget_code')->unique();
            $table->string('widget_name');
            $table->text('description')->nullable();
            $table->enum('widget_type', [
                'chart',
                'metric',
                'table',
                'list',
                'calendar',
                'map',
                'gauge',
                'progress'
            ])->default('metric');
            $table->enum('chart_type', ['line', 'bar', 'pie', 'doughnut', 'area', 'scatter'])->nullable();
            $table->json('data_source'); // Query or API endpoint configuration
            $table->json('config')->nullable(); // Widget-specific configuration
            $table->string('refresh_interval')->nullable(); // e.g., '5m', '1h', 'manual'
            $table->integer('default_width')->default(6); // Grid width (1-12)
            $table->integer('default_height')->default(4); // Grid height
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User Dashboard Configurations
        Schema::create('user_dashboards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('dashboard_name')->default('My Dashboard');
            $table->json('layout'); // Widget positions and configurations
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Analytics Events (for tracking user actions)
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('session_id')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->string('event_category'); // e.g., 'appointment', 'sale', 'navigation'
            $table->string('event_action'); // e.g., 'create', 'view', 'delete'
            $table->string('event_label')->nullable();
            $table->integer('event_value')->nullable();
            $table->string('page_url')->nullable();
            $table->string('referrer_url')->nullable();
            $table->uuid('resource_id')->nullable(); // Related record ID
            $table->string('resource_type')->nullable(); // Model class
            $table->json('metadata')->nullable(); // Additional data
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['event_category', 'event_action', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // Analytics Sessions
        Schema::create('analytics_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->string('session_id')->unique();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('page_views')->default(0);
            $table->integer('events_count')->default(0);
            $table->string('entry_page')->nullable();
            $table->string('exit_page')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->string('platform')->nullable(); // OS
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['user_id', 'started_at']);
        });

        // Performance Metrics (system performance tracking)
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('metric_name');
            $table->string('metric_category'); // database, api, queue, cache, etc.
            $table->decimal('metric_value', 15, 4);
            $table->string('unit'); // ms, MB, count, percentage
            $table->json('metadata')->nullable();
            $table->timestamp('measured_at');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['metric_category', 'metric_name', 'measured_at'], 'perf_metrics_cat_name_date_idx');
        });

        // Saved Filters (user-saved report filters)
        Schema::create('saved_filters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('filter_name');
            $table->string('filter_type'); // e.g., 'customer_list', 'sales_report'
            $table->json('filter_config'); // Filter parameters
            $table->boolean('is_default')->default(false);
            $table->boolean('is_public')->default(false); // Share with other users
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_filters');
        Schema::dropIfExists('performance_metrics');
        Schema::dropIfExists('analytics_sessions');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('user_dashboards');
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('kpi_values');
        Schema::dropIfExists('kpi_definitions');
        Schema::dropIfExists('report_executions');
        Schema::dropIfExists('report_schedules');
        Schema::dropIfExists('report_templates');
    }
};
