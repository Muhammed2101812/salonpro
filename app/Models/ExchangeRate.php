<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'date',
        'source',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'date' => 'date',
    ];
}
