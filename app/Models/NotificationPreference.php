<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationPreference extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'channel',
        'event_type',
        'is_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'preferences',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'quiet_hours_start' => 'datetime',
        'quiet_hours_end' => 'datetime',
        'preferences' => 'array',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
