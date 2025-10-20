<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BranchSetting;
use Illuminate\Support\Collection;

interface BranchSettingRepositoryInterface
{
    /**
     * Get setting by key for a branch
     */
    public function get(string $branchId, string $key, mixed $default = null): mixed;

    /**
     * Set setting for a branch
     */
    public function set(
        string $branchId,
        string $key,
        mixed $value,
        string $type = 'string',
        ?string $group = null,
        bool $isEncrypted = false
    ): BranchSetting;

    /**
     * Get all settings for a branch
     */
    public function getAllForBranch(string $branchId, ?string $group = null): array;

    /**
     * Get settings by group for a branch
     */
    public function getByGroup(string $branchId, string $group): array;

    /**
     * Delete setting for a branch
     */
    public function remove(string $branchId, string $key): bool;

    /**
     * Bulk update settings
     */
    public function bulkUpdate(string $branchId, array $settings): bool;

    /**
     * Check if setting exists
     */
    public function exists(string $branchId, string $key): bool;
}
