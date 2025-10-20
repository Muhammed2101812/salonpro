<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentReminder extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'appointment_id',
        'reminder_type',
        'hours_before',
        'status',
        'scheduled_at',
        'sent_at',
        'message',
        'error_message',
    ];

    protected $casts = [
        'hours_before' => 'integer',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isSms(): bool
    {
        return $this->reminder_type === 'sms';
    }

    public function isEmail(): bool
    {
        return $this->reminder_type === 'email';
    }

    public function isPush(): bool
    {
        return $this->reminder_type === 'push';
    }

    public function isWhatsapp(): bool
    {
        return $this->reminder_type === 'whatsapp';
    }

    public function shouldBeSent(): bool
    {
        return $this->isPending() && 
               $this->scheduled_at && 
               $this->scheduled_at->isPast();
    }
}
