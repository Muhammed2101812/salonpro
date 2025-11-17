<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface KpiDefinitionServiceInterface extends BaseServiceInterface
{
    /**
     * Find by KPI code.
     */
    public function findByCode(string $code): mixed;

    /**
     * Get active KPIs.
     */
    public function getActive(): mixed;

    /**
     * Get by category.
     */
    public function getByCategory(string $category): mixed;

    /**
     * Get by frequency.
     */
    public function getByFrequency(string $frequency): mixed;

    /**
     * Activate KPI.
     */
    public function activate(string $id): mixed;

    /**
     * Deactivate KPI.
     */
    public function deactivate(string $id): mixed;
}
