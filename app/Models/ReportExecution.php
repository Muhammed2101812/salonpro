<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExecution extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'template_id',
        'schedule_id',
        'branch_id',
        'parameters',
        'status',
        'started_at',
        'completed_at',
        'execution_time_ms',
        'row_count',
        'output_file',
        'output_format',
        'file_size',
        'error_message',
        'executed_by',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'execution_time_ms' => 'integer',
        'row_count' => 'integer',
        'file_size' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ReportSchedule::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
