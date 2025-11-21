<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface IntegrationServiceInterface extends BaseServiceInterface
{
    /**
     * Get active integrations.
     */
    public function getActive(?string $branchId = null): mixed;

    /**
     * Get by type.
     */
    public function getByType(string $type, ?string $branchId = null): mixed;

    /**
     * Get by provider.
     */
    public function getByProvider(string $provider, ?string $branchId = null): mixed;

    /**
     * Activate integration.
     */
    public function activate(string $id): mixed;

    /**
     * Deactivate integration.
     */
    public function deactivate(string $id): mixed;

    /**
     * Test connection.
     */
    public function testConnection(string $id): mixed;

    /**
     * Sync integration.
     */
    public function sync(string $id): mixed;
}
