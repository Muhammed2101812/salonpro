<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSkill extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'employee_id',
        'skill_name',
        'proficiency_level',
        'years_of_experience',
        'certified',
        'certification_date',
        'notes',
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
        'certified' => 'boolean',
        'certification_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
