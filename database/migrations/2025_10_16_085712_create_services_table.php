<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_category_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Hizmet Adı (Türkçe)
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // TL cinsinden fiyat
            $table->integer('duration_minutes'); // Dakika cinsinden süre
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('service_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
