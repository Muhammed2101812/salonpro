<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBundleItem extends Model
{
    use HasFactory;

    protected $table = 'product_bundle_items';

    public $incrementing = false;

    protected $primaryKey = ['bundle_id', 'product_id'];

    protected $fillable = [
        'bundle_id',
        'product_id',
        'quantity',
        'individual_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'individual_price' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the bundle that owns the item.
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(ProductBundle::class, 'bundle_id');
    }

    /**
     * Get the product for this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get total price for this item in the bundle.
     */
    public function getTotalPrice(): float
    {
        return (float) $this->individual_price * $this->quantity;
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('bundle_id', $this->getAttribute('bundle_id'))
              ->where('product_id', $this->getAttribute('product_id'));

        return $query;
    }
}
