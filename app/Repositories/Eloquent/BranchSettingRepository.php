<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BranchSetting;
use App\Repositories\Contracts\BranchSettingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BranchSettingRepository implements BranchSettingRepositoryInterface
{
    /**
     * Get setting by key for a branch
     */
    public function get(string $branchId, string $key, mixed $default = null): mixed
    {
        return BranchSetting::get($branchId, $key, $default);
    }

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
    ): BranchSetting {
        return BranchSetting::set($branchId, $key, $value, $type, $group, $isEncrypted);
    }

    /**
     * Get all settings for a branch
     */
    public function getAllForBranch(string $branchId, ?string $group = null): array
    {
        return BranchSetting::getAllForBranch($branchId, $group);
    }

    /**
     * Get settings by group for a branch
     */
    public function getByGroup(string $branchId, string $group): array
    {
        return BranchSetting::where('branch_id', $branchId)
            ->where('group', $group)
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Delete setting for a branch
     */
    public function remove(string $branchId, string $key): bool
    {
        return BranchSetting::remove($branchId, $key);
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(string $branchId, array $settings): bool
    {
        try {
            DB::beginTransaction();

            foreach ($settings as $key => $data) {
                $this->set(
                    $branchId,
                    $key,
                    $data['value'] ?? $data,
                    $data['type'] ?? 'string',
                    $data['group'] ?? null,
                    $data['is_encrypted'] ?? false
                );
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Check if setting exists
     */
    public function exists(string $branchId, string $key): bool
    {
        return BranchSetting::where('branch_id', $branchId)
            ->where('key', $key)
            ->exists();
    }
}
