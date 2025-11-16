<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRateLimit extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'key',
        'hits',
        'limit',
        'window',
        'reset_at',
    ];

    protected $casts = [
        'hits' => 'integer',
        'limit' => 'integer',
        'window' => 'integer',
        'reset_at' => 'datetime',
    ];
}
