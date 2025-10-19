<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Marketing Campaigns
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable(); // null = all branches
            $table->string('campaign_name');
            $table->string('campaign_code')->unique();
            $table->text('description')->nullable();
            $table->enum('campaign_type', ['email', 'sms', 'notification', 'social', 'mixed'])->default('email');
            $table->enum('campaign_objective', ['awareness', 'engagement', 'conversion', 'retention', 'reactivation'])->default('engagement');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->default(0);
            $table->integer('target_audience_size')->default(0);
            $table->integer('reached_audience')->default(0);
            $table->integer('total_conversions')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('roi', 10, 2)->default(0); // Return on Investment
            $table->enum('status', ['draft', 'scheduled', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Customer Segments
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('segment_name');
            $table->text('description')->nullable();
            $table->json('filter_criteria'); // Complex filtering rules
            $table->enum('segment_type', ['static', 'dynamic'])->default('dynamic');
            $table->integer('customer_count')->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Customer Segment Members (for static segments)
        Schema::create('customer_segment_members', function (Blueprint $table) {
            $table->uuid('segment_id');
            $table->uuid('customer_id');
            $table->timestamp('added_at')->useCurrent();
            $table->uuid('added_by')->nullable();
            $table->timestamps();

            $table->foreign('segment_id')->references('id')->on('customer_segments')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
            $table->primary(['segment_id', 'customer_id']);
        });

        // Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('coupon_code')->unique();
            $table->string('coupon_name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'free_service', 'free_product'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_limit_per_customer')->default(1);
            $table->integer('total_used')->default(0);
            $table->json('applicable_services')->nullable(); // Service IDs
            $table->json('applicable_products')->nullable(); // Product IDs
            $table->json('applicable_days')->nullable(); // Days of week
            $table->time('applicable_time_start')->nullable();
            $table->time('applicable_time_end')->nullable();
            $table->boolean('first_time_customer_only')->default(false);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Coupon Usage History
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('coupon_id');
            $table->uuid('customer_id');
            $table->uuid('appointment_id')->nullable();
            $table->uuid('sale_id')->nullable();
            $table->decimal('order_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->timestamp('used_at');
            $table->timestamps();

            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
        });

        // Loyalty Programs
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('program_name');
            $table->text('description')->nullable();
            $table->enum('point_calculation_method', ['amount_spent', 'visit_count', 'service_count', 'custom'])->default('amount_spent');
            $table->decimal('points_per_currency', 10, 2)->default(1); // Points earned per TRY spent
            $table->integer('points_per_visit')->default(0);
            $table->integer('points_per_service')->default(0);
            $table->integer('min_points_to_redeem')->default(100);
            $table->decimal('point_value', 10, 4)->default(0.1); // TRY value per point
            $table->integer('points_expiry_days')->nullable(); // Points expire after X days
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('tier_levels')->nullable(); // {bronze: {min: 0, multiplier: 1}, silver: {min: 1000, multiplier: 1.5}}
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Loyalty Points (customer points balance)
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loyalty_program_id');
            $table->uuid('customer_id');
            $table->integer('total_points_earned')->default(0);
            $table->integer('total_points_redeemed')->default(0);
            $table->integer('current_balance')->default(0);
            $table->integer('points_expiring_soon')->default(0); // Points expiring in next 30 days
            $table->string('current_tier')->nullable(); // bronze, silver, gold, platinum
            $table->decimal('tier_multiplier', 5, 2)->default(1);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->foreign('loyalty_program_id')->references('id')->on('loyalty_programs')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->unique(['loyalty_program_id', 'customer_id']);
        });

        // Loyalty Point Transactions
        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loyalty_points_id');
            $table->enum('transaction_type', ['earned', 'redeemed', 'expired', 'adjusted', 'bonus'])->default('earned');
            $table->integer('points'); // Positive for earned, negative for redeemed
            $table->integer('balance_after');
            $table->uuid('reference_id')->nullable(); // Appointment, sale, etc.
            $table->string('reference_type')->nullable();
            $table->text('description')->nullable();
            $table->date('expiry_date')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('loyalty_points_id')->references('id')->on('loyalty_points')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Referral Programs
        Schema::create('referral_programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('program_name');
            $table->text('description')->nullable();
            $table->enum('referrer_reward_type', ['discount', 'points', 'free_service', 'cash'])->default('discount');
            $table->decimal('referrer_reward_value', 10, 2);
            $table->enum('referee_reward_type', ['discount', 'points', 'free_service', 'cash'])->default('discount');
            $table->decimal('referee_reward_value', 10, 2);
            $table->integer('min_referee_purchase')->nullable(); // Min purchase for rewards
            $table->integer('max_referrals_per_customer')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Referrals
        Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('referral_program_id');
            $table->uuid('referrer_customer_id'); // Who referred
            $table->uuid('referee_customer_id')->nullable(); // Who was referred
            $table->string('referee_name')->nullable();
            $table->string('referee_phone')->nullable();
            $table->string('referee_email')->nullable();
            $table->string('referral_code')->unique();
            $table->enum('status', ['pending', 'registered', 'completed', 'rewarded', 'expired'])->default('pending');
            $table->timestamp('referred_at');
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('completed_at')->nullable(); // First purchase made
            $table->timestamp('rewarded_at')->nullable();
            $table->decimal('referee_first_purchase', 10, 2)->nullable();
            $table->boolean('referrer_rewarded')->default(false);
            $table->boolean('referee_rewarded')->default(false);
            $table->timestamps();

            $table->foreign('referral_program_id')->references('id')->on('referral_programs')->onDelete('cascade');
            $table->foreign('referrer_customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('referee_customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // Leads (potential customers)
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'])->default('new');
            $table->enum('source', ['website', 'walk_in', 'referral', 'social_media', 'advertisement', 'other'])->default('website');
            $table->string('source_details')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->integer('score')->default(0); // Lead scoring
            $table->text('notes')->nullable();
            $table->uuid('assigned_to')->nullable(); // Employee
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->uuid('converted_to_customer_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('converted_to_customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Lead Activities
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lead_id');
            $table->enum('activity_type', ['call', 'email', 'meeting', 'note', 'status_change', 'assignment'])->default('note');
            $table->text('description');
            $table->timestamp('activity_date');
            $table->uuid('user_id')->nullable();
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Customer Feedback
        Schema::create('customer_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('branch_id');
            $table->uuid('appointment_id')->nullable();
            $table->uuid('employee_id')->nullable();
            $table->integer('overall_rating'); // 1-5
            $table->integer('service_quality_rating')->nullable();
            $table->integer('cleanliness_rating')->nullable();
            $table->integer('staff_friendliness_rating')->nullable();
            $table->integer('value_rating')->nullable();
            $table->text('comment')->nullable();
            $table->text('suggestions')->nullable();
            $table->boolean('would_recommend')->nullable();
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->default('neutral');
            $table->boolean('is_published')->default(false);
            $table->uuid('responded_by')->nullable();
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('responded_by')->references('id')->on('users')->onDelete('set null');
        });

        // Surveys
        Schema::create('surveys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->nullable();
            $table->string('survey_name');
            $table->text('description')->nullable();
            $table->json('questions'); // Survey questions and configuration
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('total_sent')->default(0);
            $table->integer('total_responses')->default(0);
            $table->decimal('response_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Survey Responses
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('survey_id');
            $table->uuid('customer_id')->nullable();
            $table->json('answers'); // Question-answer pairs
            $table->timestamp('submitted_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // RFM Analysis (Recency, Frequency, Monetary)
        Schema::create('customer_rfm_analysis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->unique();
            $table->uuid('branch_id');
            $table->date('last_visit_date')->nullable();
            $table->integer('days_since_last_visit')->nullable();
            $table->integer('recency_score')->default(0); // 1-5
            $table->integer('total_visits')->default(0);
            $table->integer('frequency_score')->default(0); // 1-5
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->integer('monetary_score')->default(0); // 1-5
            $table->integer('rfm_score')->default(0); // Combined score
            $table->string('customer_segment'); // Champions, Loyal, At Risk, etc.
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_rfm_analysis');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('customer_feedback');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('referral_programs');
        Schema::dropIfExists('loyalty_point_transactions');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('loyalty_programs');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('customer_segment_members');
        Schema::dropIfExists('customer_segments');
        Schema::dropIfExists('marketing_campaigns');
    }
};
