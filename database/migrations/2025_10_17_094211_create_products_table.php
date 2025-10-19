<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Ürün Adı
            $table->text('description')->nullable(); // Açıklama
            $table->string('barcode')->nullable()->unique(); // Barkod
            $table->string('sku')->nullable()->unique(); // Stok Kodu
            $table->decimal('price', 10, 2); // Satış Fiyatı
            $table->decimal('cost_price', 10, 2)->nullable(); // Maliyet Fiyatı
            $table->integer('stock_quantity')->default(0); // Stok Miktarı
            $table->integer('min_stock_quantity')->default(0); // Minimum Stok Seviyesi
            $table->string('unit')->default('adet'); // Birim (adet, kg, litre, vb.)
            $table->string('category')->nullable(); // Kategori
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('barcode');
            $table->index('sku');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
