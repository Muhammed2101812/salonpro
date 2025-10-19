<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment']); // Giriş, Çıkış, Düzeltme
            $table->integer('quantity'); // Miktar (+ veya - olabilir)
            $table->integer('quantity_before'); // İşlemden önceki stok miktarı
            $table->integer('quantity_after'); // İşlemden sonraki stok miktarı
            $table->text('reason')->nullable(); // Neden/Açıklama
            $table->string('reference_type')->nullable(); // İlgili kayıt tipi (Sale, Purchase, vb.)
            $table->string('reference_id')->nullable(); // İlgili kayıt ID
            $table->date('movement_date'); // Hareket tarihi
            $table->timestamps();
            $table->softDeletes();

            $table->index('product_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('movement_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
