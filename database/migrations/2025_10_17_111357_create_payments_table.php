<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2); // Ödeme Tutarı
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'bank_transfer'])->default('cash'); // Nakit, Kredi Kartı, Banka Kartı, Havale
            $table->date('payment_date'); // Ödeme Tarihi
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed'); // Beklemede, Tamamlandı, Başarısız, İade
            $table->text('notes')->nullable(); // Notlar
            $table->timestamps();
            $table->softDeletes();

            $table->index('appointment_id');
            $table->index('sale_id');
            $table->index('customer_id');
            $table->index('payment_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
