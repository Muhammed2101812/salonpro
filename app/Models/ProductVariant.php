<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'barcode',
        'attributes',
        'price',
        'cost',
        'stock_quantity',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'weight_grams',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'max_stock_level' => 'integer',
        'reorder_point' => 'integer',
        'weight_grams' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
