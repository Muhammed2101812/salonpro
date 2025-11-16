<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'widget_code',
        'widget_name',
        'description',
        'widget_type',
        'chart_type',
        'data_source',
        'config',
        'refresh_interval',
        'default_width',
        'default_height',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'data_source' => 'array',
        'config' => 'array',
        'default_width' => 'integer',
        'default_height' => 'integer',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];
}
