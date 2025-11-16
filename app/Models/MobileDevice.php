<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileDevice extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'platform',
        'platform_version',
        'app_version',
        'manufacturer',
        'model',
        'is_active',
        'last_active_at',
        'ip_address',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pushTokens(): HasMany
    {
        return $this->hasMany(PushNotificationToken::class, 'device_id');
    }
}
