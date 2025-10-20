<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BranchSetting;
use App\Repositories\Contracts\BranchSettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class BranchSettingService
{
    public function __construct(
        private BranchSettingRepositoryInterface $repository
    ) {}

    /**
     * Get setting with caching
     */
    public function get(string $branchId, string $key, mixed $default = null): mixed
    {
        $cacheKey = "branch_setting:{$branchId}:{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($branchId, $key, $default) {
            return $this->repository->get($branchId, $key, $default);
        });
    }

    /**
     * Set setting and clear cache
     */
    public function set(
        string $branchId,
        string $key,
        mixed $value,
        string $type = 'string',
        ?string $group = null,
        bool $isEncrypted = false
    ): BranchSetting {
        $setting = $this->repository->set($branchId, $key, $value, $type, $group, $isEncrypted);

        // Clear cache
        $this->clearCache($branchId, $key);

        return $setting;
    }

    /**
     * Get all settings for a branch with caching
     */
    public function getAllForBranch(string $branchId, ?string $group = null): array
    {
        $cacheKey = $group
            ? "branch_settings:{$branchId}:{$group}"
            : "branch_settings:{$branchId}:all";

        return Cache::remember($cacheKey, 3600, function () use ($branchId, $group) {
            return $this->repository->getAllForBranch($branchId, $group);
        });
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $branchId, string $group): array
    {
        return $this->getAllForBranch($branchId, $group);
    }

    /**
     * Delete setting and clear cache
     */
    public function remove(string $branchId, string $key): bool
    {
        $result = $this->repository->remove($branchId, $key);

        if ($result) {
            $this->clearCache($branchId, $key);
        }

        return $result;
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(string $branchId, array $settings): bool
    {
        $result = $this->repository->bulkUpdate($branchId, $settings);

        if ($result) {
            // Clear all cache for this branch
            Cache::tags(["branch_settings:{$branchId}"])->flush();
        }

        return $result;
    }

    /**
     * Get business settings
     */
    public function getBusinessSettings(string $branchId): array
    {
        return $this->getByGroup($branchId, 'business');
    }

    /**
     * Get appointment settings
     */
    public function getAppointmentSettings(string $branchId): array
    {
        return $this->getByGroup($branchId, 'appointments');
    }

    /**
     * Get notification settings
     */
    public function getNotificationSettings(string $branchId): array
    {
        return $this->getByGroup($branchId, 'notifications');
    }

    /**
     * Get financial settings
     */
    public function getFinancialSettings(string $branchId): array
    {
        return $this->getByGroup($branchId, 'financial');
    }

    /**
     * Update business settings
     */
    public function updateBusinessSettings(string $branchId, array $settings): bool
    {
        $formattedSettings = [];

        foreach ($settings as $key => $value) {
            $formattedSettings[$key] = [
                'value' => $value,
                'type' => $this->inferType($value),
                'group' => 'business',
            ];
        }

        return $this->bulkUpdate($branchId, $formattedSettings);
    }

    /**
     * Update appointment settings
     */
    public function updateAppointmentSettings(string $branchId, array $settings): bool
    {
        $formattedSettings = [];

        foreach ($settings as $key => $value) {
            $formattedSettings[$key] = [
                'value' => $value,
                'type' => $this->inferType($value),
                'group' => 'appointments',
            ];
        }

        return $this->bulkUpdate($branchId, $formattedSettings);
    }

    /**
     * Clear cache for a specific setting
     */
    private function clearCache(string $branchId, string $key): void
    {
        Cache::forget("branch_setting:{$branchId}:{$key}");
        Cache::tags(["branch_settings:{$branchId}"])->flush();
    }

    /**
     * Infer type from value
     */
    private function inferType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'json',
            default => 'string',
        };
    }
}
