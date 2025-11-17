<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface WebhookServiceInterface extends BaseServiceInterface
{
    /**
     * Get active webhooks.
     */
    public function getActive(?string $branchId = null): mixed;

    /**
     * Get by event.
     */
    public function getByEvent(string $event, ?string $branchId = null): mixed;

    /**
     * Trigger webhook.
     */
    public function trigger(string $id, string $event, array $payload): mixed;

    /**
     * Activate webhook.
     */
    public function activate(string $id): mixed;

    /**
     * Deactivate webhook.
     */
    public function deactivate(string $id): mixed;

    /**
     * Test webhook.
     */
    public function test(string $id): mixed;
}
