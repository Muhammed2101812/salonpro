<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'attribute_name',
        'attribute_code',
        'attribute_type',
        'options',
        'is_filterable',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_filterable' => 'boolean',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the attribute values for this attribute.
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    /**
     * Check if attribute is of select type.
     */
    public function isSelectType(): bool
    {
        return in_array($this->attribute_type, ['select', 'multiselect']);
    }

    /**
     * Check if attribute has options.
     */
    public function hasOptions(): bool
    {
        return !empty($this->options);
    }

    /**
     * Add option to attribute.
     */
    public function addOption(string $option): void
    {
        $options = $this->options ?? [];

        if (!in_array($option, $options)) {
            $options[] = $option;
            $this->update(['options' => $options]);
        }
    }

    /**
     * Remove option from attribute.
     */
    public function removeOption(string $option): void
    {
        $options = $this->options ?? [];

        $options = array_filter($options, fn($opt) => $opt !== $option);

        $this->update(['options' => array_values($options)]);
    }

    /**
     * Validate value against attribute type and options.
     */
    public function isValidValue(mixed $value): bool
    {
        switch ($this->attribute_type) {
            case 'select':
                return in_array($value, $this->options ?? []);

            case 'multiselect':
                if (!is_array($value)) {
                    return false;
                }
                return empty(array_diff($value, $this->options ?? []));

            case 'number':
                return is_numeric($value);

            case 'boolean':
                return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false']);

            case 'text':
            default:
                return is_string($value) || is_numeric($value);
        }
    }

    /**
     * Scope to filterable attributes.
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope to required attributes.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope to ordered attributes.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('attribute_name');
    }

    /**
     * Get available attribute types.
     */
    public static function getAvailableTypes(): array
    {
        return [
            'text' => 'Metin',
            'number' => 'Sayı',
            'select' => 'Seçim (Tekli)',
            'multiselect' => 'Seçim (Çoklu)',
            'boolean' => 'Evet/Hayır',
        ];
    }
}
