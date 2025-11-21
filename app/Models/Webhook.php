<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'branch_id',
        'name',
        'url',
        'events',
        'secret',
        'is_active',
        'timeout',
        'max_retries',
        'headers',
        'last_triggered_at',
        'success_count',
        'failure_count',
        'created_by',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'timeout' => 'integer',
        'max_retries' => 'integer',
        'headers' => 'array',
        'last_triggered_at' => 'datetime',
        'success_count' => 'integer',
        'failure_count' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }
}
