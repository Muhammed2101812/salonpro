<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'http_status',
        'response_body',
        'attempt',
        'duration_ms',
        'status',
        'error_message',
        'sent_at',
        'next_retry_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'http_status' => 'integer',
        'attempt' => 'integer',
        'duration_ms' => 'integer',
        'sent_at' => 'datetime',
        'next_retry_at' => 'datetime',
    ];

    /**
     * Get the webhook that owns the log
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    /**
     * Check if the delivery was successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if the delivery failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the delivery is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the delivery is retrying
     */
    public function isRetrying(): bool
    {
        return $this->status === 'retrying';
    }

    /**
     * Check if should retry
     */
    public function shouldRetry(): bool
    {
        return $this->isRetrying() && 
               $this->next_retry_at && 
               $this->next_retry_at->isPast() &&
               $this->attempt < $this->webhook->max_retries;
    }

    /**
     * Scope to get failed deliveries
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get successful deliveries
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to get pending deliveries
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get deliveries that need retry
     */
    public function scopeNeedsRetry($query)
    {
        return $query->where('status', 'retrying')
            ->where('next_retry_at', '<=', now());
    }
}
