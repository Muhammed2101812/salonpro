<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSupplierPrice extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'supplier_sku',
        'price',
        'currency',
        'minimum_order_quantity',
        'lead_time_days',
        'is_preferred',
        'price_valid_from',
        'price_valid_until',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'minimum_order_quantity' => 'integer',
        'lead_time_days' => 'integer',
        'is_preferred' => 'boolean',
        'price_valid_from' => 'date',
        'price_valid_until' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
