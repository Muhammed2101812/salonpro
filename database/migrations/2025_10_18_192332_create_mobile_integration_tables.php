<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Note: personal_access_tokens table is created by Laravel Sanctum migration

        // API Rate Limits
        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique(); // user:123, ip:192.168.1.1, token:abc
            $table->integer('hits')->default(0);
            $table->integer('limit')->default(60); // Max requests
            $table->integer('window')->default(60); // Time window in seconds
            $table->timestamp('reset_at');
            $table->timestamps();

            $table->index(['key', 'reset_at']);
        });

        // Webhooks (Outgoing)
        Schema::create('webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('name');
            $table->string('url');
            $table->json('events'); // ['appointment.created', 'customer.updated']
            $table->string('secret')->nullable(); // For signature verification
            $table->boolean('is_active')->default(true);
            $table->integer('timeout')->default(30); // Seconds
            $table->integer('max_retries')->default(3);
            $table->json('headers')->nullable(); // Custom headers
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Webhook Delivery Logs
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('webhook_id');
            $table->string('event');
            $table->json('payload');
            $table->integer('http_status')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('attempt')->default(1);
            $table->integer('duration_ms')->nullable(); // Response time
            $table->enum('status', ['pending', 'success', 'failed', 'retrying'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->foreign('webhook_id')->references('id')->on('webhooks')->onDelete('cascade');
            $table->index(['webhook_id', 'status', 'created_at']);
        });

        // Mobile Devices
        Schema::create('mobile_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('device_id')->unique(); // Unique device identifier
            $table->string('device_name')->nullable();
            $table->enum('platform', ['ios', 'android', 'web'])->default('android');
            $table->string('platform_version')->nullable();
            $table->string('app_version')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_active_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Push Notification Tokens
        Schema::create('push_notification_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('device_id')->nullable();
            $table->string('token')->unique();
            $table->enum('provider', ['fcm', 'apns', 'web_push'])->default('fcm');
            $table->json('metadata')->nullable(); // Additional device info
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('mobile_devices')->onDelete('set null');
        });

        // Third Party Integrations
        Schema::create('integrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('integration_name'); // e.g., "WhatsApp Business", "Google Calendar"
            $table->string('integration_type'); // whatsapp, calendar, payment, crm, etc.
            $table->string('provider'); // e.g., "twilio", "google", "stripe"
            $table->json('credentials'); // Encrypted API keys, tokens
            $table->json('settings')->nullable(); // Integration-specific settings
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_synced_at')->nullable();
            $table->enum('status', ['connected', 'disconnected', 'error', 'expired'])->default('disconnected');
            $table->text('error_message')->nullable();
            $table->uuid('configured_by');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('configured_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Integration Activity Logs
        Schema::create('integration_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('integration_id');
            $table->enum('action', ['sync', 'send', 'receive', 'connect', 'disconnect', 'error'])->default('sync');
            $table->string('resource_type')->nullable(); // appointment, customer, etc.
            $table->uuid('resource_id')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('http_status')->nullable();
            $table->enum('status', ['success', 'failed', 'partial'])->default('success');
            $table->text('error_message')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->foreign('integration_id')->references('id')->on('integrations')->onDelete('cascade');
            $table->index(['integration_id', 'status', 'executed_at']);
        });

        // OAuth Providers (for OAuth integrations)
        Schema::create('oauth_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider_name'); // google, facebook, microsoft
            $table->string('provider_key')->unique();
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('redirect_uri');
            $table->json('scopes')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // OAuth Tokens
        Schema::create('oauth_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('provider_id');
            $table->string('access_token');
            $table->string('refresh_token')->nullable();
            $table->string('token_type')->default('Bearer');
            $table->integer('expires_in')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('scopes')->nullable();
            $table->json('metadata')->nullable(); // Provider user info
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('oauth_providers')->onDelete('cascade');
            $table->unique(['user_id', 'provider_id']);
        });

        // API Activity Logs (for monitoring API usage)
        Schema::create('api_activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('token_id')->nullable(); // personal_access_tokens id
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('method', 10); // GET, POST, etc.
            $table->string('endpoint');
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('http_status');
            $table->integer('response_time_ms')->nullable();
            $table->timestamp('requested_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['user_id', 'requested_at']);
            $table->index(['endpoint', 'http_status']);
        });

        // API Endpoint Documentation (auto-generated API docs)
        Schema::create('api_endpoints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('method', 10);
            $table->string('path');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->json('parameters')->nullable();
            $table->json('request_body')->nullable();
            $table->json('response_examples')->nullable();
            $table->json('scopes_required')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_deprecated')->default(false);
            $table->string('version')->default('v1');
            $table->timestamps();

            $table->unique(['method', 'path', 'version']);
        });

        // SMS Gateway Logs (for SMS integrations)
        Schema::create('sms_gateway_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sms_provider_id')->nullable();
            $table->string('provider_name');
            $table->string('to_number');
            $table->string('from_number')->nullable();
            $table->text('message');
            $table->enum('status', ['queued', 'sent', 'delivered', 'failed', 'bounced'])->default('queued');
            $table->string('provider_message_id')->nullable();
            $table->json('provider_response')->nullable();
            $table->decimal('cost', 10, 4)->nullable();
            $table->integer('message_parts')->default(1); // SMS parts
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('sms_provider_id')->references('id')->on('sms_providers')->onDelete('set null');
            $table->index(['to_number', 'status', 'created_at']);
        });

        // Email Gateway Logs (for email integrations)
        Schema::create('email_gateway_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('email_provider_id')->nullable();
            $table->string('provider_name');
            $table->string('to_email');
            $table->string('from_email');
            $table->string('subject');
            $table->text('body_preview')->nullable();
            $table->enum('status', ['queued', 'sent', 'delivered', 'opened', 'clicked', 'failed', 'bounced', 'spam'])->default('queued');
            $table->string('provider_message_id')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('email_provider_id')->references('id')->on('email_providers')->onDelete('set null');
            $table->index(['to_email', 'status', 'created_at']);
        });

        // Third Party Sync Status (for syncing data with external systems)
        Schema::create('sync_status', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('integration_id');
            $table->string('resource_type'); // appointments, customers, etc.
            $table->uuid('local_id'); // Local record ID
            $table->string('remote_id')->nullable(); // External system ID
            $table->enum('sync_direction', ['push', 'pull', 'bidirectional'])->default('bidirectional');
            $table->enum('status', ['synced', 'pending', 'conflict', 'error'])->default('pending');
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('integration_id')->references('id')->on('integrations')->onDelete('cascade');
            $table->unique(['integration_id', 'resource_type', 'local_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_status');
        Schema::dropIfExists('email_gateway_logs');
        Schema::dropIfExists('sms_gateway_logs');
        Schema::dropIfExists('api_endpoints');
        Schema::dropIfExists('api_activity_logs');
        Schema::dropIfExists('oauth_tokens');
        Schema::dropIfExists('oauth_providers');
        Schema::dropIfExists('integration_logs');
        Schema::dropIfExists('integrations');
        Schema::dropIfExists('push_notification_tokens');
        Schema::dropIfExists('mobile_devices');
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_rate_limits');
        // Note: personal_access_tokens is managed by Laravel Sanctum migration
    }
};
