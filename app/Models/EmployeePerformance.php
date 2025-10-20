<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePerformance extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'employee_performance';

    protected $fillable = [
        'employee_id',
        'evaluation_date',
        'customer_satisfaction_score',
        'punctuality_score',
        'sales_performance_score',
        'teamwork_score',
        'total_sales',
        'total_appointments',
        'notes',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'customer_satisfaction_score' => 'integer',
        'punctuality_score' => 'integer',
        'sales_performance_score' => 'integer',
        'teamwork_score' => 'integer',
        'total_sales' => 'decimal:2',
        'total_appointments' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getAverageScore(): float
    {
        $scores = array_filter([
            $this->customer_satisfaction_score,
            $this->punctuality_score,
            $this->sales_performance_score,
            $this->teamwork_score,
        ]);

        return count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
    }
}
