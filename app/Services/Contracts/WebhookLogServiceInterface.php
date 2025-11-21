<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface WebhookLogServiceInterface extends BaseServiceInterface
{
    /**
     * Get by webhook.
     */
    public function getByWebhook(string $webhookId, int $perPage = 15): mixed;

    /**
     * Get failed logs.
     */
    public function getFailed(): mixed;

    /**
     * Get pending retries.
     */
    public function getPendingRetries(): mixed;

    /**
     * Retry webhook.
     */
    public function retry(string $id): mixed;
}
