<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCertification extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'employee_id',
        'certification_name',
        'issuing_organization',
        'issue_date',
        'expiry_date',
        'certificate_number',
        'is_verified',
        'document_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_verified' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
