<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDashboard extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'dashboard_name',
        'layout',
        'is_default',
    ];

    protected $casts = [
        'layout' => 'array',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
