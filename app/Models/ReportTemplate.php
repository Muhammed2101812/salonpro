<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'template_name',
        'template_code',
        'description',
        'category',
        'parameters',
        'columns',
        'query',
        'output_format',
        'template_file',
        'is_system',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'parameters' => 'array',
        'columns' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all schedules for this template
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ReportSchedule::class, 'template_id');
    }

    /**
     * Get all executions for this template
     */
    public function executions(): HasMany
    {
        return $this->hasMany(ReportExecution::class, 'template_id');
    }

    /**
     * Scope to get only active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get templates by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get non-system templates (user-created)
     */
    public function scopeUserCreated($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Check if template can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system;
    }

    /**
     * Get latest execution for this template
     */
    public function latestExecution()
    {
        return $this->executions()->latest()->first();
    }
}
