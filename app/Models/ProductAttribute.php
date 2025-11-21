<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'attribute_name',
        'attribute_code',
        'attribute_type',
        'options',
        'is_filterable',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_filterable' => 'boolean',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }
}
