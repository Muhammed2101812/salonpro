<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsSession extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'branch_id',
        'session_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'page_views',
        'events_count',
        'entry_page',
        'exit_page',
        'device_type',
        'browser',
        'platform',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_seconds' => 'integer',
        'page_views' => 'integer',
        'events_count' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
