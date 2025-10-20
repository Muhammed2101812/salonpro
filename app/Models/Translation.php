<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'field',
        'locale',
        'value',
    ];

    /**
     * Get the parent translatable model.
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by locale.
     */
    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope to filter by field.
     */
    public function scopeForField($query, string $field)
    {
        return $query->where('field', $field);
    }

    /**
     * Get translation for a specific model, field, and locale.
     */
    public static function getTranslation($model, string $field, string $locale): ?string
    {
        $translation = self::where([
            'translatable_type' => get_class($model),
            'translatable_id' => $model->id,
            'field' => $field,
            'locale' => $locale,
        ])->first();

        return $translation?->value;
    }

    /**
     * Set translation for a specific model, field, and locale.
     */
    public static function setTranslation($model, string $field, string $locale, string $value): self
    {
        return self::updateOrCreate(
            [
                'translatable_type' => get_class($model),
                'translatable_id' => $model->id,
                'field' => $field,
                'locale' => $locale,
            ],
            ['value' => $value]
        );
    }
}
