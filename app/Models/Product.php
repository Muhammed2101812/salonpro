<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'barcode',
        'sku',
        'price',
        'cost_price',
        'stock_quantity',
        'min_stock_quantity',
        'unit',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_stock_quantity' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Check if product is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_quantity;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Get the purchase order items for the product.
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the stock alerts for the product.
     */
    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    /**
     * Get the stock transfers for the product.
     */
    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }

    /**
     * Get the stock audit items for the product.
     */
    public function stockAuditItems(): HasMany
    {
        return $this->hasMany(StockAuditItem::class);
    }

    /**
     * Get the bundles this product belongs to.
     */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(ProductBundle::class, 'product_bundle_items', 'product_id', 'bundle_id')
            ->withPivot('quantity', 'individual_price')
            ->withTimestamps();
    }

    /**
     * Get active stock alerts for the product.
     */
    public function activeStockAlerts(): HasMany
    {
        return $this->stockAlerts()->where('status', 'active');
    }

    /**
     * Get critical stock alerts for the product.
     */
    public function criticalStockAlerts(): HasMany
    {
        return $this->stockAlerts()->where('severity', 'critical')->where('status', 'active');
    }

    /**
     * Get product variants.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get active variants.
     */
    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    /**
     * Get product images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the primary image.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get product barcodes.
     */
    public function barcodes(): HasMany
    {
        return $this->hasMany(ProductBarcode::class);
    }

    /**
     * Get the primary barcode.
     */
    public function primaryBarcode(): HasOne
    {
        return $this->hasOne(ProductBarcode::class)->where('is_primary', true);
    }

    /**
     * Get product discounts.
     */
    public function discounts(): HasMany
    {
        return $this->hasMany(ProductDiscount::class);
    }

    /**
     * Get active discounts.
     */
    public function activeDiscounts(): HasMany
    {
        return $this->hasMany(ProductDiscount::class)->where('is_active', true);
    }

    /**
     * Get supplier prices.
     */
    public function supplierPrices(): HasMany
    {
        return $this->hasMany(ProductSupplierPrice::class);
    }

    /**
     * Get preferred supplier price.
     */
    public function preferredSupplierPrice(): HasOne
    {
        return $this->hasOne(ProductSupplierPrice::class)->where('is_preferred', true);
    }

    /**
     * Get category hierarchy.
     */
    public function categoryHierarchy(): HasOne
    {
        return $this->hasOne(ProductCategoryHierarchy::class, 'category_id');
    }

    /**
     * Get product attributes.
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_attribute_values', 'product_id', 'attribute_id')
            ->withPivot('attribute_value')
            ->withTimestamps();
    }

    /**
     * Get attribute values.
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Get price history.
     */
    public function priceHistory(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class);
    }

    /**
     * Get stock history.
     */
    public function stockHistory(): HasMany
    {
        return $this->hasMany(ProductStockHistory::class);
    }

    /**
     * Get the best available discount for given quantity.
     */
    public function getBestDiscount(int $quantity = 1, ?string $branchId = null): ?ProductDiscount
    {
        $discounts = $this->activeDiscounts()
            ->where(function ($query) use ($branchId) {
                $query->whereNull('branch_id')
                    ->orWhere('branch_id', $branchId);
            })
            ->get()
            ->filter(fn($discount) => $discount->isValid());

        $bestDiscount = null;
        $maxDiscountAmount = 0;

        foreach ($discounts as $discount) {
            $amount = $discount->calculateDiscountAmount($this->price, $quantity);
            if ($amount > $maxDiscountAmount) {
                $maxDiscountAmount = $amount;
                $bestDiscount = $discount;
            }
        }

        return $bestDiscount;
    }

    /**
     * Get product attribute value by code.
     */
    public function getProductAttributeValue(string $code): mixed
    {
        $attributeValue = $this->attributeValues()
            ->whereHas('attribute', function ($query) use ($code) {
                $query->where('attribute_code', $code);
            })
            ->first();

        return $attributeValue?->getFormattedValue();
    }
}
