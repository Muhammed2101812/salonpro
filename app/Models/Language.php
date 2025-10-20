<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'locale',
        'flag',
        'direction',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all translations for this language.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'locale', 'locale');
    }

    /**
     * Get active languages only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the default language.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true)->first();
    }

    /**
     * Set this language as default.
     */
    public function setAsDefault(): void
    {
        // Remove default from all languages
        self::query()->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }

    /**
     * Check if this is RTL language.
     */
    public function isRtl(): bool
    {
        return $this->direction === 'rtl';
    }

    /**
     * Get ordered languages.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
