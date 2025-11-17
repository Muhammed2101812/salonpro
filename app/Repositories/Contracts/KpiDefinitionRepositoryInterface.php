<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface KpiDefinitionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find by KPI code.
     */
    public function findByCode(string $code): mixed;

    /**
     * Get active KPIs.
     */
    public function getActive(): Collection;

    /**
     * Get by category.
     */
    public function getByCategory(string $category): Collection;

    /**
     * Get by frequency.
     */
    public function getByFrequency(string $frequency): Collection;
}
