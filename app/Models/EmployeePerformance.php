<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePerformance extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'employee_id',
        'review_date',
        'reviewer_id',
        'rating',
        'strengths',
        'areas_for_improvement',
        'goals',
        'comments',
        'next_review_date',
    ];

    protected $casts = [
        'review_date' => 'date',
        'rating' => 'decimal:2',
        'next_review_date' => 'date',
        'strengths' => 'array',
        'areas_for_improvement' => 'array',
        'goals' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
