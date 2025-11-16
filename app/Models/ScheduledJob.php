<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledJob extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'job_name',
        'job_class',
        'cron_expression',
        'parameters',
        'is_active',
        'last_run_at',
        'next_run_at',
        'run_count',
        'failure_count',
        'last_error',
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'run_count' => 'integer',
        'failure_count' => 'integer',
    ];

    public function executionLogs(): HasMany
    {
        return $this->hasMany(JobExecutionLog::class);
    }
}
