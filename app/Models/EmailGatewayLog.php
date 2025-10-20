<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailGatewayLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'email_provider_id',
        'provider_name',
        'to_email',
        'from_email',
        'subject',
        'body_preview',
        'status',
        'provider_message_id',
        'provider_response',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'error_message',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    /**
     * Get the email provider (if exists)
     */
    public function emailProvider(): BelongsTo
    {
        return $this->belongsTo(EmailProvider::class, 'email_provider_id');
    }

    /**
     * Check if email was delivered
     */
    public function isDelivered(): bool
    {
        return in_array($this->status, ['delivered', 'opened', 'clicked']);
    }

    /**
     * Check if email was opened
     */
    public function isOpened(): bool
    {
        return in_array($this->status, ['opened', 'clicked']);
    }

    /**
     * Check if email was clicked
     */
    public function isClicked(): bool
    {
        return $this->status === 'clicked';
    }

    /**
     * Check if email failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'bounced', 'spam']);
    }

    /**
     * Check if email is queued
     */
    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    /**
     * Get time to open (from sent to opened)
     */
    public function getTimeToOpenAttribute(): ?int
    {
        if ($this->sent_at && $this->opened_at) {
            return $this->sent_at->diffInSeconds($this->opened_at);
        }
        return null;
    }

    /**
     * Get time to click (from sent to clicked)
     */
    public function getTimeToClickAttribute(): ?int
    {
        if ($this->sent_at && $this->clicked_at) {
            return $this->sent_at->diffInSeconds($this->clicked_at);
        }
        return null;
    }

    /**
     * Scope to get delivered emails
     */
    public function scopeDelivered($query)
    {
        return $query->whereIn('status', ['delivered', 'opened', 'clicked']);
    }

    /**
     * Scope to get opened emails
     */
    public function scopeOpened($query)
    {
        return $query->whereIn('status', ['opened', 'clicked']);
    }

    /**
     * Scope to get clicked emails
     */
    public function scopeClicked($query)
    {
        return $query->where('status', 'clicked');
    }

    /**
     * Scope to get failed emails
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'bounced', 'spam']);
    }

    /**
     * Scope to get queued emails
     */
    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    /**
     * Scope to get emails by recipient
     */
    public function scopeToEmail($query, string $email)
    {
        return $query->where('to_email', $email);
    }

    /**
     * Scope to get emails by provider
     */
    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider_name', $provider);
    }
}
