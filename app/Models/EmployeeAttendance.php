<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'employee_id',
        'branch_id',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'total_hours',
        'status',
        'notes',
        'ip_address',
        'location',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'total_hours' => 'decimal:2',
        'location' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
