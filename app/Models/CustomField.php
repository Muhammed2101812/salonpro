<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    use HasFactory, HasUuids;

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

    /**
     * Get all values for this custom field.
     */
    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    /**
     * Scope to filter by model type.
     */
    public function scopeForModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to get active fields only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get required fields only.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope to get searchable fields only.
     */
    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    /**
     * Scope to get fields that should be shown in list.
     */
    public function scopeShowInList($query)
    {
        return $query->where('show_in_list', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('field_label');
    }

    /**
     * Get value for a specific model.
     */
    public function getValueFor(Model $model)
    {
        $fieldValue = CustomFieldValue::where([
            'custom_field_id' => $this->id,
            'model_type' => get_class($model),
            'model_id' => $model->id,
        ])->first();

        return $fieldValue?->value ?? $this->default_value;
    }

    /**
     * Set value for a specific model.
     */
    public function setValueFor(Model $model, $value): CustomFieldValue
    {
        return CustomFieldValue::updateOrCreate(
            [
                'custom_field_id' => $this->id,
                'model_type' => get_class($model),
                'model_id' => $model->id,
            ],
            ['value' => $value]
        );
    }

    /**
     * Validate a value against field rules.
     */
    public function validateValue($value): bool
    {
        if ($this->is_required && empty($value)) {
            return false;
        }

        // Add more validation based on field_type
        return true;
    }
}
