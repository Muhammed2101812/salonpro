<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'employee_attendance';

    protected $fillable = [
        'employee_id',
        'branch_id',
        'attendance_date',
        'check_in',
        'check_out',
        'total_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'total_hours' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    public function isPresent(): bool
    {
        return $this->status === 'present';
    }
}
