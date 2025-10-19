<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category'); // Gider Kategorisi
            $table->string('title'); // Gider Başlığı
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2); // Tutar
            $table->date('expense_date'); // Gider Tarihi
            $table->string('payment_method')->nullable(); // Ödeme Yöntemi (nakit, kredi kartı, vb.)
            $table->string('receipt_number')->nullable(); // Fiş/Fatura No
            $table->timestamps();
            $table->softDeletes();

            $table->index('branch_id');
            $table->index('category');
            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
