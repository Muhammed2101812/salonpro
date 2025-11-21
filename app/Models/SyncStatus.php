<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncStatus extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'sync_status';

    protected $fillable = [
        'integration_id',
        'resource_type',
        'local_id',
        'remote_id',
        'sync_direction',
        'status',
        'last_synced_at',
        'sync_attempts',
        'error_message',
        'conflict_data',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'sync_attempts' => 'integer',
        'conflict_data' => 'array',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
