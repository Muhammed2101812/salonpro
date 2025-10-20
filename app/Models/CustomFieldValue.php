<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomFieldValue extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'custom_field_id',
        'model_type',
        'model_id',
        'value',
    ];

    /**
     * Get the custom field definition.
     */
    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    /**
     * Get the parent model.
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the casted value based on field type.
     */
    public function getCastedValue()
    {
        $field = $this->customField;

        if (!$field) {
            return $this->value;
        }

        return match ($field->field_type) {
            'number', 'integer' => (int) $this->value,
            'decimal', 'float' => (float) $this->value,
            'boolean', 'checkbox' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'date' => $this->value ? \Carbon\Carbon::parse($this->value) : null,
            'json', 'array' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Scope to filter by custom field.
     */
    public function scopeForField($query, $customFieldId)
    {
        return $query->where('custom_field_id', $customFieldId);
    }

    /**
     * Scope to filter by model.
     */
    public function scopeForModel($query, string $modelType, string $modelId)
    {
        return $query->where('model_type', $modelType)
            ->where('model_id', $modelId);
    }

    /**
     * Get all custom field values for a model.
     */
    public static function getValuesFor(Model $model): array
    {
        return self::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->with('customField')
            ->get()
            ->mapWithKeys(function ($fieldValue) {
                return [$fieldValue->customField->field_name => $fieldValue->getCastedValue()];
            })
            ->toArray();
    }
}
