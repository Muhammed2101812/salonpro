<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiActivityLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'token_id',
        'ip_address',
        'user_agent',
        'method',
        'endpoint',
        'request_data',
        'response_data',
        'http_status',
        'response_time_ms',
        'requested_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'http_status' => 'integer',
        'response_time_ms' => 'integer',
        'requested_at' => 'datetime',
    ];

    /**
     * Get the user that made the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the request was successful (2xx status)
     */
    public function isSuccessful(): bool
    {
        return $this->http_status >= 200 && $this->http_status < 300;
    }

    /**
     * Check if the request was a client error (4xx status)
     */
    public function isClientError(): bool
    {
        return $this->http_status >= 400 && $this->http_status < 500;
    }

    /**
     * Check if the request was a server error (5xx status)
     */
    public function isServerError(): bool
    {
        return $this->http_status >= 500 && $this->http_status < 600;
    }

    /**
     * Check if the request was slow (> 1 second)
     */
    public function isSlow(int $thresholdMs = 1000): bool
    {
        return $this->response_time_ms > $thresholdMs;
    }

    /**
     * Scope to get successful requests
     */
    public function scopeSuccessful($query)
    {
        return $query->whereBetween('http_status', [200, 299]);
    }

    /**
     * Scope to get failed requests
     */
    public function scopeFailed($query)
    {
        return $query->where('http_status', '>=', 400);
    }

    /**
     * Scope to get client errors
     */
    public function scopeClientErrors($query)
    {
        return $query->whereBetween('http_status', [400, 499]);
    }

    /**
     * Scope to get server errors
     */
    public function scopeServerErrors($query)
    {
        return $query->whereBetween('http_status', [500, 599]);
    }

    /**
     * Scope to get slow requests
     */
    public function scopeSlow($query, int $thresholdMs = 1000)
    {
        return $query->where('response_time_ms', '>', $thresholdMs);
    }

    /**
     * Scope to get requests by endpoint
     */
    public function scopeEndpoint($query, string $endpoint)
    {
        return $query->where('endpoint', $endpoint);
    }

    /**
     * Scope to get requests by method
     */
    public function scopeMethod($query, string $method)
    {
        return $query->where('method', strtoupper($method));
    }

    /**
     * Scope to get recent requests
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('requested_at', '>=', now()->subHours($hours));
    }
}
