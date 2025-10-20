<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'invoice_id',
        'item_type',
        'service_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'discount_percentage',
        'discount_amount',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Helper Methods
    public function calculateSubtotal(): float
    {
        return (float) ($this->quantity * $this->unit_price);
    }

    public function calculateDiscountAmount(): float
    {
        $subtotal = $this->calculateSubtotal();
        return (float) ($subtotal * ($this->discount_percentage / 100));
    }

    public function calculateTaxAmount(): float
    {
        $subtotal = $this->calculateSubtotal();
        $afterDiscount = $subtotal - $this->discount_amount;
        return (float) ($afterDiscount * ($this->tax_rate / 100));
    }

    public function calculateTotal(): float
    {
        $subtotal = $this->calculateSubtotal();
        $afterDiscount = $subtotal - $this->discount_amount;
        return (float) ($afterDiscount + $this->tax_amount);
    }

    public function recalculate(): void
    {
        $this->subtotal = $this->calculateSubtotal();
        $this->discount_amount = $this->calculateDiscountAmount();
        $this->tax_amount = $this->calculateTaxAmount();
        $this->total = $this->calculateTotal();
        $this->save();
    }

    // Get the related item (service or product)
    public function getItemAttribute()
    {
        return match ($this->item_type) {
            'service' => $this->service,
            'product' => $this->product,
            default => null,
        };
    }
}
