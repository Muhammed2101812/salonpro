<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('channel', ['email', 'sms', 'push', 'whatsapp']);
            $table->string('event_type');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['channel', 'event_type']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
