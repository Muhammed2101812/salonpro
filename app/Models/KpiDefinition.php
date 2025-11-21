<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiDefinition extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'kpi_code',
        'kpi_name',
        'description',
        'category',
        'calculation_method',
        'calculation_formula',
        'unit',
        'frequency',
        'target_value',
        'warning_threshold',
        'critical_threshold',
        'higher_is_better',
        'is_active',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'warning_threshold' => 'decimal:2',
        'critical_threshold' => 'decimal:2',
        'higher_is_better' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(KpiValue::class);
    }
}
