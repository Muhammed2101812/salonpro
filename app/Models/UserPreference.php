<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'language',
        'currency',
        'timezone',
        'date_format',
        'time_format',
        'theme',
        'notifications',
        'dashboard_layout',
        'custom_settings',
    ];

    protected $casts = [
        'notifications' => 'array',
        'dashboard_layout' => 'array',
        'custom_settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
