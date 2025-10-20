<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'name',
        'url',
        'events',
        'secret',
        'is_active',
        'timeout',
        'max_retries',
        'headers',
        'last_triggered_at',
        'success_count',
        'failure_count',
        'created_by',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'is_active' => 'boolean',
        'timeout' => 'integer',
        'max_retries' => 'integer',
        'success_count' => 'integer',
        'failure_count' => 'integer',
        'last_triggered_at' => 'datetime',
    ];

    /**
     * Get the branch that owns the webhook
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the webhook
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the webhook logs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }

    /**
     * Get recent logs
     */
    public function recentLogs(int $limit = 10): HasMany
    {
        return $this->logs()->latest()->limit($limit);
    }

    /**
     * Check if webhook listens to a specific event
     */
    public function listensTo(string $event): bool
    {
        return in_array($event, $this->events ?? []);
    }

    /**
     * Increment success count
     */
    public function incrementSuccess(): void
    {
        $this->increment('success_count');
        $this->update(['last_triggered_at' => now()]);
    }

    /**
     * Increment failure count
     */
    public function incrementFailure(): void
    {
        $this->increment('failure_count');
        $this->update(['last_triggered_at' => now()]);
    }

    /**
     * Get success rate as percentage
     */
    public function successRate(): float
    {
        $total = $this->success_count + $this->failure_count;
        return $total > 0 ? ($this->success_count / $total) * 100 : 0;
    }

    /**
     * Scope to get only active webhooks
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get webhooks for a specific event
     */
    public function scopeForEvent($query, string $event)
    {
        return $query->whereJsonContains('events', $event);
    }
}
