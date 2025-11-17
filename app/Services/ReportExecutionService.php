<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ReportExecutionRepositoryInterface;
use App\Services\Contracts\ReportExecutionServiceInterface;
use Illuminate\Support\Facades\DB;

class ReportExecutionService extends BaseService implements ReportExecutionServiceInterface
{
    public function __construct(
        protected ReportExecutionRepositoryInterface $reportExecutionRepository
    ) {
        parent::__construct($reportExecutionRepository);
    }

    public function getByTemplate(string $templateId, int $perPage = 15): mixed
    {
        return $this->reportExecutionRepository->getByTemplate($templateId, $perPage);
    }

    public function getPending(): mixed
    {
        return $this->reportExecutionRepository->getPending();
    }

    public function getCompleted(?string $branchId = null): mixed
    {
        return $this->reportExecutionRepository->getCompleted($branchId);
    }

    public function getFailed(?string $branchId = null): mixed
    {
        return $this->reportExecutionRepository->getFailed($branchId);
    }

    public function executeReport(array $data): mixed
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = 'pending';
            $data['started_at'] = now();

            return $this->reportExecutionRepository->create($data);
        });
    }

    public function markAsCompleted(string $id, array $data): mixed
    {
        return DB::transaction(function () use ($id, $data) {
            $execution = $this->reportExecutionRepository->findOrFail($id);

            if ($execution->status !== 'pending') {
                throw new \RuntimeException('Only pending executions can be marked as completed');
            }

            $completedAt = now();
            $executionTime = $execution->started_at->diffInMilliseconds($completedAt);

            return $this->reportExecutionRepository->update($id, array_merge($data, [
                'status' => 'completed',
                'completed_at' => $completedAt,
                'execution_time_ms' => $executionTime,
            ]));
        });
    }

    public function markAsFailed(string $id, string $errorMessage): mixed
    {
        return DB::transaction(function () use ($id, $errorMessage) {
            $execution = $this->reportExecutionRepository->findOrFail($id);

            if ($execution->status !== 'pending') {
                throw new \RuntimeException('Only pending executions can be marked as failed');
            }

            return $this->reportExecutionRepository->update($id, [
                'status' => 'failed',
                'completed_at' => now(),
                'error_message' => $errorMessage,
            ]);
        });
    }
}
