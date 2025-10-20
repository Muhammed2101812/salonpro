<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileDevice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'platform',
        'platform_version',
        'app_version',
        'manufacturer',
        'model',
        'is_active',
        'last_active_at',
        'ip_address',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    /**
     * Get the user that owns the device
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the push notification tokens for this device
     */
    public function pushTokens(): HasMany
    {
        return $this->hasMany(PushNotificationToken::class, 'device_id');
    }

    /**
     * Get the active push token for this device
     */
    public function activePushToken()
    {
        return $this->pushTokens()->where('is_active', true)->first();
    }

    /**
     * Update the last active timestamp
     */
    public function touch($attribute = null)
    {
        $this->last_active_at = now();
        return parent::touch($attribute);
    }

    /**
     * Mark device as inactive
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
        $this->pushTokens()->update(['is_active' => false]);
    }

    /**
     * Mark device as active
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Get device display name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->device_name) {
            return $this->device_name;
        }

        $parts = array_filter([
            $this->manufacturer,
            $this->model,
        ]);

        return !empty($parts) ? implode(' ', $parts) : $this->platform;
    }

    /**
     * Scope to get only active devices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get devices by platform
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get recently active devices
     */
    public function scopeRecentlyActive($query, int $minutes = 30)
    {
        return $query->where('last_active_at', '>=', now()->subMinutes($minutes));
    }
}
