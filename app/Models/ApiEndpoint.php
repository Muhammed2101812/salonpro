<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiEndpoint extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'method',
        'path',
        'name',
        'description',
        'parameters',
        'request_body',
        'response_examples',
        'scopes_required',
        'is_public',
        'is_deprecated',
        'version',
    ];

    protected $casts = [
        'parameters' => 'array',
        'request_body' => 'array',
        'response_examples' => 'array',
        'scopes_required' => 'array',
        'is_public' => 'boolean',
        'is_deprecated' => 'boolean',
    ];

    /**
     * Get the full endpoint path with version
     */
    public function getFullPathAttribute(): string
    {
        return "/api/{$this->version}{$this->path}";
    }

    /**
     * Check if endpoint requires authentication
     */
    public function requiresAuthentication(): bool
    {
        return !$this->is_public;
    }

    /**
     * Check if endpoint has required scopes
     */
    public function hasRequiredScopes(): bool
    {
        return !empty($this->scopes_required);
    }

    /**
     * Scope to get only public endpoints
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get only authenticated endpoints
     */
    public function scopeAuthenticated($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope to get only active (non-deprecated) endpoints
     */
    public function scopeActive($query)
    {
        return $query->where('is_deprecated', false);
    }

    /**
     * Scope to get deprecated endpoints
     */
    public function scopeDeprecated($query)
    {
        return $query->where('is_deprecated', true);
    }

    /**
     * Scope to get endpoints by version
     */
    public function scopeVersion($query, string $version)
    {
        return $query->where('version', $version);
    }

    /**
     * Scope to get endpoints by method
     */
    public function scopeMethod($query, string $method)
    {
        return $query->where('method', strtoupper($method));
    }
}
