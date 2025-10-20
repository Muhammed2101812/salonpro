<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'default_data',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'default_data' => 'array',
            'is_system' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Check if template can be deleted (system templates cannot be deleted)
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system;
    }

    /**
     * Create a service from this template
     */
    public function createService(array $overrides = []): array
    {
        return array_merge($this->default_data, $overrides);
    }

    /**
     * Get default value for a specific field
     */
    public function getDefaultValue(string $field, $default = null)
    {
        return $this->default_data[$field] ?? $default;
    }
}
