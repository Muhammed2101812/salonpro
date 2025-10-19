<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notification Templates
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable(); // null = global template
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['email', 'sms', 'push', 'whatsapp'])->default('sms');
            $table->enum('event', [
                'appointment_created',
                'appointment_confirmed',
                'appointment_reminder',
                'appointment_cancelled',
                'appointment_completed',
                'appointment_no_show',
                'payment_received',
                'payment_pending',
                'birthday_greeting',
                'promotion',
                'feedback_request',
                'custom'
            ])->default('appointment_reminder');
            $table->string('subject')->nullable(); // For email
            $table->text('body_template'); // With variables like {{customer_name}}, {{appointment_date}}
            $table->json('available_variables')->nullable(); // List of available variables
            $table->json('default_values')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // System templates can't be deleted
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Notification Queue
        Schema::create('notification_queue', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('template_id')->nullable();
            $table->enum('type', ['email', 'sms', 'push', 'whatsapp'])->default('sms');
            $table->string('recipient'); // Email, phone number, device token
            $table->uuid('customer_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('variables')->nullable(); // Variables used in this notification
            $table->enum('status', ['pending', 'processing', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('notification_templates')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'scheduled_at']);
        });

        // Notification Preferences (per customer)
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->unique();
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->boolean('whatsapp_enabled')->default(false);
            $table->boolean('appointment_reminders')->default(true);
            $table->boolean('appointment_confirmations')->default(true);
            $table->boolean('promotional_messages')->default(true);
            $table->boolean('birthday_wishes')->default(true);
            $table->boolean('feedback_requests')->default(true);
            $table->json('preferred_times')->nullable(); // e.g., {"start": "09:00", "end": "21:00"}
            $table->json('quiet_hours')->nullable(); // Hours to not send notifications
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });

        // Notification Logs (sent notifications)
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('notification_queue_id')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->enum('type', ['email', 'sms', 'push', 'whatsapp'])->default('sms');
            $table->string('recipient');
            $table->text('message');
            $table->enum('status', ['sent', 'delivered', 'failed', 'bounced'])->default('sent');
            $table->timestamp('sent_at');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->decimal('cost', 10, 4)->nullable(); // Cost of sending (for SMS)
            $table->string('provider')->nullable(); // SMS/Email provider used
            $table->string('provider_message_id')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamps();

            $table->foreign('notification_queue_id')->references('id')->on('notification_queue')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->index(['customer_id', 'sent_at']);
        });

        // SMS Provider Settings
        Schema::create('sms_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable(); // null = global
            $table->string('name'); // Netgsm, Twilio, etc.
            $table->string('provider_code'); // netgsm, twilio, etc.
            $table->json('credentials'); // Encrypted API keys, etc.
            $table->boolean('is_active')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->integer('priority')->default(0);
            $table->decimal('cost_per_sms', 10, 4)->nullable();
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Email Provider Settings
        Schema::create('email_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('name'); // SMTP, SendGrid, Mailgun, etc.
            $table->string('provider_code');
            $table->json('credentials');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->string('from_email');
            $table->string('from_name');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Scheduled Campaigns
        Schema::create('notification_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('template_id');
            $table->enum('type', ['email', 'sms', 'push', 'whatsapp'])->default('sms');
            $table->json('target_criteria'); // Filter criteria for recipients
            $table->integer('estimated_recipients')->default(0);
            $table->integer('actual_recipients')->default(0);
            $table->timestamp('scheduled_at');
            $table->enum('status', ['draft', 'scheduled', 'processing', 'completed', 'cancelled'])->default('draft');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('notification_templates')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Campaign Statistics
        Schema::create('campaign_statistics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('campaign_id');
            $table->integer('total_sent')->default(0);
            $table->integer('total_delivered')->default(0);
            $table->integer('total_failed')->default(0);
            $table->integer('total_bounced')->default(0);
            $table->integer('total_opened')->default(0);
            $table->integer('total_clicked')->default(0);
            $table->decimal('delivery_rate', 5, 2)->default(0);
            $table->decimal('open_rate', 5, 2)->default(0);
            $table->decimal('click_rate', 5, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('notification_campaigns')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_statistics');
        Schema::dropIfExists('notification_campaigns');
        Schema::dropIfExists('email_providers');
        Schema::dropIfExists('sms_providers');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notification_queue');
        Schema::dropIfExists('notification_templates');
    }
};
