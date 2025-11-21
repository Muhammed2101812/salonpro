<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface WebhookRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active webhooks.
     */
    public function getActive(?string $branchId = null): Collection;

    /**
     * Get by event.
     */
    public function getByEvent(string $event, ?string $branchId = null): Collection;

    /**
     * Get by branch.
     */
    public function getByBranch(string $branchId): Collection;

    /**
     * Increment success count.
     */
    public function incrementSuccessCount(string $id): mixed;

    /**
     * Increment failure count.
     */
    public function incrementFailureCount(string $id): mixed;

    /**
     * Update last triggered.
     */
    public function updateLastTriggered(string $id): mixed;
}
