<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OauthToken extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'provider_id',
        'access_token',
        'refresh_token',
        'token_type',
        'expires_in',
        'expires_at',
        'scopes',
        'metadata',
    ];

    protected $casts = [
        'expires_in' => 'integer',
        'expires_at' => 'datetime',
        'scopes' => 'array',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the user that owns the token
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the OAuth provider
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(OauthProvider::class, 'provider_id');
    }

    /**
     * Check if the token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the token is valid
     */
    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Check if token needs refresh (expires in less than 5 minutes)
     */
    public function needsRefresh(): bool
    {
        return $this->expires_at && $this->expires_at->subMinutes(5)->isPast();
    }

    /**
     * Get the full authorization header value
     */
    public function getAuthorizationHeader(): string
    {
        return "{$this->token_type} {$this->access_token}";
    }

    /**
     * Scope to get valid tokens
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to get expired tokens
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Scope to get tokens that need refresh
     */
    public function scopeNeedsRefresh($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addMinutes(5));
    }
}
