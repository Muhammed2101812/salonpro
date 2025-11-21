<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ReportExecution;
use App\Repositories\Contracts\ReportExecutionRepositoryInterface;
use Illuminate\Support\Collection;

class ReportExecutionRepository extends BaseRepository implements ReportExecutionRepositoryInterface
{
    public function __construct(ReportExecution $model)
    {
        parent::__construct($model);
    }

    public function getByTemplate(string $templateId, int $perPage = 15): mixed
    {
        return $this->model->with(['template', 'branch', 'executor'])
            ->where('template_id', $templateId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->with(['template', 'branch', 'executor'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPending(): Collection
    {
        return $this->model->with(['template', 'branch', 'executor'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getCompleted(?string $branchId = null): Collection
    {
        $query = $this->model->with(['template', 'branch', 'executor'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getFailed(?string $branchId = null): Collection
    {
        $query = $this->model->with(['template', 'branch', 'executor'])
            ->where('status', 'failed')
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getByBranch(string $branchId, int $perPage = 15): mixed
    {
        return $this->model->with(['template', 'branch', 'executor'])
            ->where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->with(['template', 'branch', 'executor'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
