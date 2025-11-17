<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ReportExecutionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get executions by template.
     */
    public function getByTemplate(string $templateId, int $perPage = 15): mixed;

    /**
     * Get executions by status.
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get pending executions.
     */
    public function getPending(): Collection;

    /**
     * Get completed executions.
     */
    public function getCompleted(?string $branchId = null): Collection;

    /**
     * Get failed executions.
     */
    public function getFailed(?string $branchId = null): Collection;

    /**
     * Get executions by branch.
     */
    public function getByBranch(string $branchId, int $perPage = 15): mixed;

    /**
     * Get recent executions.
     */
    public function getRecent(int $limit = 10): Collection;
}
