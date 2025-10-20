<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotificationToken extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'device_id',
        'token',
        'provider',
        'metadata',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the token
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the device associated with the token
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(MobileDevice::class, 'device_id');
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Deactivate the token
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate the token
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Check if token is for FCM (Firebase Cloud Messaging)
     */
    public function isFcm(): bool
    {
        return $this->provider === 'fcm';
    }

    /**
     * Check if token is for APNS (Apple Push Notification Service)
     */
    public function isApns(): bool
    {
        return $this->provider === 'apns';
    }

    /**
     * Check if token is for Web Push
     */
    public function isWebPush(): bool
    {
        return $this->provider === 'web_push';
    }

    /**
     * Scope to get only active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get tokens by provider
     */
    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to get FCM tokens
     */
    public function scopeFcm($query)
    {
        return $query->where('provider', 'fcm');
    }

    /**
     * Scope to get APNS tokens
     */
    public function scopeApns($query)
    {
        return $query->where('provider', 'apns');
    }
}
