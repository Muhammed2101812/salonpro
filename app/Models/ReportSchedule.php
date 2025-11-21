<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSchedule extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'template_id',
        'branch_id',
        'schedule_name',
        'frequency',
        'schedule_config',
        'parameters',
        'recipients',
        'last_run_at',
        'next_run_at',
        'status',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'schedule_config' => 'array',
        'parameters' => 'array',
        'recipients' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
