<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedFilter extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'filter_name',
        'filter_type',
        'filter_config',
        'is_default',
        'is_public',
    ];

    protected $casts = [
        'filter_config' => 'array',
        'is_default' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Get the user who owns this filter
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get public filters
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get private filters
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope to get default filters
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get filters by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('filter_type', $type);
    }

    /**
     * Scope to get accessible filters for a user
     */
    public function scopeAccessibleBy($query, string $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('is_public', true);
        });
    }

    /**
     * Set this filter as default for the user and type
     */
    public function setAsDefault(): bool
    {
        // Remove default flag from other filters of same type
        static::where('user_id', $this->user_id)
            ->where('filter_type', $this->filter_type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        return $this->update(['is_default' => true]);
    }

    /**
     * Make filter public
     */
    public function makePublic(): bool
    {
        return $this->update(['is_public' => true]);
    }

    /**
     * Make filter private
     */
    public function makePrivate(): bool
    {
        return $this->update(['is_public' => false]);
    }

    /**
     * Clone filter for another user
     */
    public function cloneFor(string $userId, ?string $newName = null): self
    {
        return static::create([
            'user_id' => $userId,
            'filter_name' => $newName ?? $this->filter_name . ' (Kopya)',
            'filter_type' => $this->filter_type,
            'filter_config' => $this->filter_config,
            'is_default' => false,
            'is_public' => false,
        ]);
    }

    /**
     * Apply filter to a query builder
     */
    public function apply($query)
    {
        $config = $this->filter_config;

        foreach ($config as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Check if user can edit this filter
     */
    public function canBeEditedBy(string $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Check if user can delete this filter
     */
    public function canBeDeletedBy(string $userId): bool
    {
        return $this->user_id === $userId;
    }
}
