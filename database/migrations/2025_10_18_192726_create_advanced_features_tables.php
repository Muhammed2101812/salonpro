<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Languages
        Schema::create('languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // English, Türkçe
            $table->string('code', 5)->unique(); // en, tr
            $table->string('locale', 10)->unique(); // en_US, tr_TR
            $table->string('flag')->nullable(); // Flag emoji or image
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Translations (for dynamic content)
        Schema::create('translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('translatable_type'); // Model class name
            $table->uuid('translatable_id');
            $table->string('field'); // Field name (title, description, etc.)
            $table->string('locale', 10);
            $table->text('value');
            $table->timestamps();

            $table->index(['translatable_type', 'translatable_id']);
            $table->index(['locale']);
            $table->unique(['translatable_type', 'translatable_id', 'field', 'locale'], 'translations_unique');
        });

        // Currencies
        Schema::create('currencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 3)->unique(); // TRY, USD, EUR
            $table->string('name'); // Turkish Lira, US Dollar
            $table->string('symbol'); // ₺, $, €
            $table->string('symbol_position')->default('before'); // before, after
            $table->integer('decimal_places')->default(2);
            $table->string('thousands_separator')->default(',');
            $table->string('decimal_separator')->default('.');
            $table->decimal('exchange_rate', 15, 6)->default(1); // Relative to base currency
            $table->boolean('is_base')->default(false); // Base currency for conversions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Exchange Rate History
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 15, 6);
            $table->date('date');
            $table->string('source')->nullable(); // API source
            $table->timestamps();

            $table->index(['from_currency', 'to_currency', 'date']);
        });

        // System Backups
        Schema::create('system_backups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('backup_name');
            $table->enum('backup_type', ['full', 'database', 'files', 'partial'])->default('database');
            $table->string('file_path');
            $table->bigInteger('file_size')->default(0); // Bytes
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->json('backup_info')->nullable(); // Tables, files included
            $table->text('error_message')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Audit Logs (system-wide activity tracking)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('auditable_type'); // Model class
            $table->uuid('auditable_id');
            $table->string('event'); // created, updated, deleted, restored
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->text('tags')->nullable(); // Searchable tags
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('event');
        });

        // System Settings (global application settings)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('group'); // general, email, sms, etc.
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, number, boolean, json, array
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_public')->default(false); // Accessible without auth
            $table->timestamps();
        });

        // Custom Fields (dynamic fields for any model)
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model_type'); // Customer, Appointment, etc.
            $table->string('field_name');
            $table->string('field_label');
            $table->string('field_type'); // text, number, select, date, checkbox, etc.
            $table->json('field_options')->nullable(); // For select, radio, etc.
            $table->text('default_value')->nullable();
            $table->string('validation_rules')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->boolean('show_in_list')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['model_type', 'field_name']);
        });

        // Custom Field Values
        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('custom_field_id');
            $table->string('model_type');
            $table->uuid('model_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('custom_field_id')->references('id')->on('custom_fields')->onDelete('cascade');
            $table->index(['model_type', 'model_id']);
            $table->unique(['custom_field_id', 'model_type', 'model_id']);
        });

        // File Uploads
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('uploadable_type')->nullable(); // Model class
            $table->uuid('uploadable_id')->nullable();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('disk')->default('local');
            $table->string('mime_type');
            $table->bigInteger('file_size'); // Bytes
            $table->string('file_type'); // image, document, video, etc.
            $table->json('metadata')->nullable(); // Dimensions, duration, etc.
            $table->integer('download_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['uploadable_type', 'uploadable_id']);
        });

        // Document Templates (for generating PDFs, invoices, etc.)
        Schema::create('document_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('template_name');
            $table->string('template_code')->unique();
            $table->enum('template_type', ['invoice', 'receipt', 'contract', 'report', 'certificate', 'custom'])->default('custom');
            $table->text('description')->nullable();
            $table->text('template_content'); // HTML or blade template
            $table->json('variables')->nullable(); // Available variables
            $table->string('paper_size')->default('A4');
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->json('header_html')->nullable();
            $table->json('footer_html')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Activity Logs (user activity timeline)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('log_name')->nullable(); // authentication, sales, etc.
            $table->text('description');
            $table->string('subject_type')->nullable(); // Model class
            $table->uuid('subject_id')->nullable();
            $table->string('causer_type')->nullable(); // Who caused the activity
            $table->uuid('causer_id')->nullable();
            $table->json('properties')->nullable(); // Additional data
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['subject_type', 'subject_id']);
            $table->index(['causer_type', 'causer_id']);
            $table->index('log_name');
        });

        // Feature Flags (enable/disable features dynamically)
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('feature_key')->unique();
            $table->string('feature_name');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->json('conditions')->nullable(); // Conditions for enabling
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // User Preferences (individual user settings)
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('language', 5)->default('tr');
            $table->string('currency', 3)->default('TRY');
            $table->string('timezone')->default('Europe/Istanbul');
            $table->string('date_format')->default('d/m/Y');
            $table->string('time_format')->default('H:i');
            $table->enum('theme', ['light', 'dark', 'auto'])->default('light');
            $table->json('notifications')->nullable(); // Notification preferences
            $table->json('dashboard_layout')->nullable(); // Custom dashboard
            $table->json('custom_settings')->nullable(); // Additional preferences
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Import History (data import tracking)
        Schema::create('import_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('import_type'); // customers, products, appointments
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('total_rows')->default(0);
            $table->integer('successful_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('mapping')->nullable(); // Column mapping
            $table->json('errors')->nullable(); // Import errors
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Export History (data export tracking)
        Schema::create('export_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('export_type'); // customers, sales, reports
            $table->enum('format', ['csv', 'excel', 'pdf', 'json'])->default('excel');
            $table->json('filters')->nullable(); // Export filters
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('total_rows')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Scheduled Jobs (cron jobs management)
        Schema::create('scheduled_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('job_name');
            $table->string('job_class'); // Class name
            $table->string('cron_expression'); // */5 * * * *
            $table->json('parameters')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->integer('run_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        // Job Execution Logs
        Schema::create('job_execution_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('scheduled_job_id')->nullable();
            $table->string('job_class');
            $table->json('parameters')->nullable();
            $table->enum('status', ['running', 'completed', 'failed'])->default('running');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->text('output')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('scheduled_job_id')->references('id')->on('scheduled_jobs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_execution_logs');
        Schema::dropIfExists('scheduled_jobs');
        Schema::dropIfExists('export_history');
        Schema::dropIfExists('import_history');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('document_templates');
        Schema::dropIfExists('file_uploads');
        Schema::dropIfExists('custom_field_values');
        Schema::dropIfExists('custom_fields');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('system_backups');
        Schema::dropIfExists('exchange_rates');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('languages');
    }
};
