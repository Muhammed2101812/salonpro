<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'decimal_places',
        'thousands_separator',
        'decimal_separator',
        'exchange_rate',
        'is_base',
        'is_active',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'exchange_rate' => 'decimal:6',
        'is_base' => 'boolean',
        'is_active' => 'boolean',
    ];
}
