<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = ['product_id', 'attribute_id'];

    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'attribute_id',
        'attribute_value',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns this attribute value.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute definition.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Get formatted value based on attribute type.
     */
    public function getFormattedValue(): mixed
    {
        if (!$this->attribute) {
            return $this->attribute_value;
        }

        switch ($this->attribute->attribute_type) {
            case 'number':
                return (float) $this->attribute_value;

            case 'boolean':
                return filter_var($this->attribute_value, FILTER_VALIDATE_BOOLEAN);

            case 'multiselect':
                return is_string($this->attribute_value)
                    ? json_decode($this->attribute_value, true)
                    : $this->attribute_value;

            default:
                return $this->attribute_value;
        }
    }

    /**
     * Set value with validation.
     */
    public function setValueWithValidation(mixed $value): bool
    {
        if (!$this->attribute) {
            return false;
        }

        if (!$this->attribute->isValidValue($value)) {
            return false;
        }

        // Convert to string for storage
        if (is_array($value)) {
            $value = json_encode($value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        $this->attribute_value = (string) $value;
        $this->save();

        return true;
    }

    /**
     * Scope by attribute code.
     */
    public function scopeByAttributeCode($query, string $code)
    {
        return $query->whereHas('attribute', function ($q) use ($code) {
            $q->where('attribute_code', $code);
        });
    }

    /**
     * Scope by attribute value.
     */
    public function scopeByValue($query, mixed $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        return $query->where('attribute_value', $value);
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('product_id', $this->getAttribute('product_id'))
              ->where('attribute_id', $this->getAttribute('attribute_id'));

        return $query;
    }
}
