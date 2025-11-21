<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentReminder extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'appointment_id',
        'reminder_type',
        'send_at',
        'sent_at',
        'status',
        'channel',
        'recipient',
        'message',
        'error_message',
    ];

    protected $casts = [
        'send_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
