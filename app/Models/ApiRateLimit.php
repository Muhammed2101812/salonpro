<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRateLimit extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'key',
        'hits',
        'limit',
        'window',
        'reset_at',
    ];

    protected $casts = [
        'hits' => 'integer',
        'limit' => 'integer',
        'window' => 'integer',
        'reset_at' => 'datetime',
    ];

    /**
     * Increment the hit count for this rate limit key
     */
    public function incrementHits(): void
    {
        $this->increment('hits');
    }

    /**
     * Check if the rate limit has been exceeded
     */
    public function isExceeded(): bool
    {
        return $this->hits >= $this->limit;
    }

    /**
     * Check if the rate limit window has expired
     */
    public function isExpired(): bool
    {
        return $this->reset_at->isPast();
    }

    /**
     * Reset the rate limit
     */
    public function reset(): void
    {
        $this->update([
            'hits' => 0,
            'reset_at' => now()->addSeconds($this->window),
        ]);
    }

    /**
     * Get remaining requests allowed
     */
    public function remaining(): int
    {
        return max(0, $this->limit - $this->hits);
    }
}
