<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OauthProvider extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'provider_name',
        'provider_key',
        'client_id',
        'client_secret',
        'redirect_uri',
        'scopes',
        'config',
        'is_active',
    ];

    protected $casts = [
        'scopes' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'client_secret',
    ];

    /**
     * Get the OAuth tokens for this provider
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(OauthToken::class, 'provider_id');
    }

    /**
     * Get the authorization URL
     */
    public function getAuthorizationUrl(array $additionalParams = []): string
    {
        $params = array_merge([
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'response_type' => 'code',
            'scope' => implode(' ', $this->scopes ?? []),
        ], $additionalParams);

        $baseUrl = $this->config['authorization_url'] ?? '';
        return $baseUrl . '?' . http_build_query($params);
    }

    /**
     * Scope to get only active providers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get provider by key
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('provider_key', $key);
    }

    /**
     * Check if provider is Google
     */
    public function isGoogle(): bool
    {
        return $this->provider_key === 'google';
    }

    /**
     * Check if provider is Facebook
     */
    public function isFacebook(): bool
    {
        return $this->provider_key === 'facebook';
    }

    /**
     * Check if provider is Microsoft
     */
    public function isMicrosoft(): bool
    {
        return $this->provider_key === 'microsoft';
    }
}
