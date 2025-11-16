<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'channel',
        'event_type',
        'subject',
        'body',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
