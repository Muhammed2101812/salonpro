<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncStatus extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'integration_id',
        'resource_type',
        'local_id',
        'remote_id',
        'sync_direction',
        'status',
        'last_synced_at',
        'sync_metadata',
        'error_message',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'sync_metadata' => 'array',
    ];

    /**
     * Get the integration that owns the sync status
     */
    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    /**
     * Check if synced
     */
    public function isSynced(): bool
    {
        return $this->status === 'synced';
    }

    /**
     * Check if pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if has conflict
     */
    public function hasConflict(): bool
    {
        return $this->status === 'conflict';
    }

    /**
     * Check if has error
     */
    public function hasError(): bool
    {
        return $this->status === 'error';
    }

    /**
     * Check if push direction
     */
    public function isPush(): bool
    {
        return $this->sync_direction === 'push';
    }

    /**
     * Check if pull direction
     */
    public function isPull(): bool
    {
        return $this->sync_direction === 'pull';
    }

    /**
     * Check if bidirectional
     */
    public function isBidirectional(): bool
    {
        return $this->sync_direction === 'bidirectional';
    }

    /**
     * Mark as synced
     */
    public function markAsSynced(string $remoteId = null): void
    {
        $this->update([
            'status' => 'synced',
            'last_synced_at' => now(),
            'remote_id' => $remoteId ?? $this->remote_id,
            'error_message' => null,
        ]);
    }

    /**
     * Mark as error
     */
    public function markAsError(string $message): void
    {
        $this->update([
            'status' => 'error',
            'error_message' => $message,
        ]);
    }

    /**
     * Mark as conflict
     */
    public function markAsConflict(string $message = null): void
    {
        $this->update([
            'status' => 'conflict',
            'error_message' => $message,
        ]);
    }

    /**
     * Scope to get synced records
     */
    public function scopeSynced($query)
    {
        return $query->where('status', 'synced');
    }

    /**
     * Scope to get pending records
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get conflict records
     */
    public function scopeConflicts($query)
    {
        return $query->where('status', 'conflict');
    }

    /**
     * Scope to get error records
     */
    public function scopeErrors($query)
    {
        return $query->where('status', 'error');
    }

    /**
     * Scope to get by resource type
     */
    public function scopeResourceType($query, string $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope to get by local ID
     */
    public function scopeLocalId($query, string $localId)
    {
        return $query->where('local_id', $localId);
    }

    /**
     * Scope to get by remote ID
     */
    public function scopeRemoteId($query, string $remoteId)
    {
        return $query->where('remote_id', $remoteId);
    }
}
