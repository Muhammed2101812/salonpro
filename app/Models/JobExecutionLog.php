<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobExecutionLog extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'scheduled_job_id',
        'job_class',
        'parameters',
        'status',
        'started_at',
        'completed_at',
        'duration_ms',
        'output',
        'error_message',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_ms' => 'integer',
    ];

    public function scheduledJob(): BelongsTo
    {
        return $this->belongsTo(ScheduledJob::class);
    }
}
