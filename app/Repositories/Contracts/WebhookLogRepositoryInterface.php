<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface WebhookLogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get by webhook.
     */
    public function getByWebhook(string $webhookId, int $perPage = 15): mixed;

    /**
     * Get by status.
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get failed logs.
     */
    public function getFailed(): Collection;

    /**
     * Get pending retries.
     */
    public function getPendingRetries(): Collection;

    /**
     * Get by event.
     */
    public function getByEvent(string $event): Collection;

    /**
     * Get recent logs.
     */
    public function getRecent(int $limit = 50): Collection;
}
