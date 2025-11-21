<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'model_type',
        'field_name',
        'field_label',
        'field_type',
        'field_options',
        'default_value',
        'validation_rules',
        'help_text',
        'is_required',
        'is_searchable',
        'show_in_list',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
        'is_searchable' => 'boolean',
        'show_in_list' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }
}
