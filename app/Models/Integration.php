<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Integration extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'integration_name',
        'integration_type',
        'provider',
        'credentials',
        'settings',
        'is_active',
        'last_synced_at',
        'status',
        'error_message',
        'configured_by',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get the branch that owns the integration
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who configured the integration
     */
    public function configuredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'configured_by');
    }

    /**
     * Get the integration logs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(IntegrationLog::class);
    }

    /**
     * Get the sync status records
     */
    public function syncStatuses(): HasMany
    {
        return $this->hasMany(SyncStatus::class);
    }

    /**
     * Get recent logs
     */
    public function recentLogs(int $limit = 50)
    {
        return $this->logs()->latest('executed_at')->limit($limit)->get();
    }

    /**
     * Check if integration is connected
     */
    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    /**
     * Check if integration has error
     */
    public function hasError(): bool
    {
        return $this->status === 'error';
    }

    /**
     * Check if integration is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Mark integration as connected
     */
    public function markAsConnected(): void
    {
        $this->update([
            'status' => 'connected',
            'error_message' => null,
        ]);
    }

    /**
     * Mark integration as disconnected
     */
    public function markAsDisconnected(): void
    {
        $this->update([
            'status' => 'disconnected',
            'is_active' => false,
        ]);
    }

    /**
     * Mark integration as error
     */
    public function markAsError(string $message): void
    {
        $this->update([
            'status' => 'error',
            'error_message' => $message,
            'is_active' => false,
        ]);
    }

    /**
     * Update last synced timestamp
     */
    public function updateLastSynced(): void
    {
        $this->update(['last_synced_at' => now()]);
    }

    /**
     * Scope to get only active integrations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get integrations by type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('integration_type', $type);
    }

    /**
     * Scope to get connected integrations
     */
    public function scopeConnected($query)
    {
        return $query->where('status', 'connected');
    }
}
