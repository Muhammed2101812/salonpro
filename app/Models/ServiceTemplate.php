<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTemplate extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'name',
        'description',
        'default_data',
        'is_system',
    ];

    protected $casts = [
        'default_data' => 'array',
        'is_system' => 'boolean',
    ];
}
