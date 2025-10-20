<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'integration_id',
        'action',
        'resource_type',
        'resource_id',
        'request_data',
        'response_data',
        'http_status',
        'status',
        'error_message',
        'duration_ms',
        'executed_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'http_status' => 'integer',
        'duration_ms' => 'integer',
        'executed_at' => 'datetime',
    ];

    /**
     * Get the integration that owns the log
     */
    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    /**
     * Check if the action was successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if the action failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the action was partial
     */
    public function isPartial(): bool
    {
        return $this->status === 'partial';
    }

    /**
     * Scope to get successful logs
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to get failed logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get logs by action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get logs by resource type
     */
    public function scopeResourceType($query, string $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope to get logs for a specific resource
     */
    public function scopeForResource($query, string $resourceType, string $resourceId)
    {
        return $query->where('resource_type', $resourceType)
            ->where('resource_id', $resourceId);
    }
}
