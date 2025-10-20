<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsGatewayLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'sms_provider_id',
        'provider_name',
        'to_number',
        'from_number',
        'message',
        'status',
        'provider_message_id',
        'provider_response',
        'cost',
        'message_parts',
        'sent_at',
        'delivered_at',
        'error_message',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'cost' => 'decimal:4',
        'message_parts' => 'integer',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the SMS provider (if exists)
     */
    public function smsProvider(): BelongsTo
    {
        return $this->belongsTo(SmsProvider::class, 'sms_provider_id');
    }

    /**
     * Check if SMS was delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if SMS was sent
     */
    public function isSent(): bool
    {
        return in_array($this->status, ['sent', 'delivered']);
    }

    /**
     * Check if SMS failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'bounced']);
    }

    /**
     * Check if SMS is queued
     */
    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    /**
     * Get message length
     */
    public function getMessageLengthAttribute(): int
    {
        return mb_strlen($this->message);
    }

    /**
     * Scope to get delivered SMS
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope to get sent SMS
     */
    public function scopeSent($query)
    {
        return $query->whereIn('status', ['sent', 'delivered']);
    }

    /**
     * Scope to get failed SMS
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'bounced']);
    }

    /**
     * Scope to get queued SMS
     */
    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    /**
     * Scope to get SMS by phone number
     */
    public function scopeToNumber($query, string $number)
    {
        return $query->where('to_number', $number);
    }

    /**
     * Scope to get SMS by provider
     */
    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider_name', $provider);
    }
}
