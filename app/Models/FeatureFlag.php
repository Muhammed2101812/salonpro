<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'feature_key',
        'feature_name',
        'description',
        'is_enabled',
        'conditions',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'conditions' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
