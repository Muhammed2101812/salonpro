<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBarcode extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'barcode',
        'barcode_type',
        'is_primary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns the barcode.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Set this barcode as primary.
     */
    public function setPrimary(): void
    {
        // Remove primary flag from other barcodes
        static::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        $this->update(['is_primary' => true]);
    }

    /**
     * Scope to primary barcodes.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope by barcode type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('barcode_type', $type);
    }

    /**
     * Get available barcode types.
     */
    public static function getAvailableTypes(): array
    {
        return [
            'ean13' => 'EAN-13',
            'ean8' => 'EAN-8',
            'upc' => 'UPC',
            'code128' => 'Code 128',
            'code39' => 'Code 39',
            'qr' => 'QR Code',
        ];
    }
}
