<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface ReportExecutionServiceInterface extends BaseServiceInterface
{
    /**
     * Get executions by template.
     */
    public function getByTemplate(string $templateId, int $perPage = 15): mixed;

    /**
     * Get pending executions.
     */
    public function getPending(): mixed;

    /**
     * Get completed executions.
     */
    public function getCompleted(?string $branchId = null): mixed;

    /**
     * Get failed executions.
     */
    public function getFailed(?string $branchId = null): mixed;

    /**
     * Execute report.
     */
    public function executeReport(array $data): mixed;

    /**
     * Mark as completed.
     */
    public function markAsCompleted(string $id, array $data): mixed;

    /**
     * Mark as failed.
     */
    public function markAsFailed(string $id, string $errorMessage): mixed;
}
