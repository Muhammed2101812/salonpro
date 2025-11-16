<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'session_id',
        'branch_id',
        'event_category',
        'event_action',
        'event_label',
        'event_value',
        'page_url',
        'referrer_url',
        'resource_id',
        'resource_type',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'event_value' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
