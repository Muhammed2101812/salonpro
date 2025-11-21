<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface IntegrationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active integrations.
     */
    public function getActive(?string $branchId = null): Collection;

    /**
     * Get by type.
     */
    public function getByType(string $type, ?string $branchId = null): Collection;

    /**
     * Get by provider.
     */
    public function getByProvider(string $provider, ?string $branchId = null): Collection;

    /**
     * Get by branch.
     */
    public function getByBranch(string $branchId): Collection;

    /**
     * Update last synced.
     */
    public function updateLastSynced(string $id): mixed;

    /**
     * Update status.
     */
    public function updateStatus(string $id, string $status, ?string $errorMessage = null): mixed;
}
