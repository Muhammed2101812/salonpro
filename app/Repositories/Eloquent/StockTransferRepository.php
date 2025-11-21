<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\StockTransfer;
use App\Repositories\Contracts\StockTransferRepositoryInterface;

class StockTransferRepository extends BaseRepository implements StockTransferRepositoryInterface
{
    public function __construct(StockTransfer $model)
    {
        parent::__construct($model);
    }

    public function findBySourceBranch(string $branchId, int $perPage = 15)
    {
        return $this->model->where('from_branch_id', $branchId)
            ->with(['fromBranch', 'toBranch', 'productVariant.product', 'createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByDestinationBranch(string $branchId, int $perPage = 15)
    {
        return $this->model->where('to_branch_id', $branchId)
            ->with(['fromBranch', 'toBranch', 'productVariant.product', 'createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByStatus(string $status, int $perPage = 15)
    {
        return $this->model->where('status', $status)
            ->with(['fromBranch', 'toBranch', 'productVariant.product', 'createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getPendingTransfers(?string $branchId = null, int $perPage = 15)
    {
        $query = $this->model->where('status', 'pending')
            ->with(['fromBranch', 'toBranch', 'productVariant.product', 'createdBy']);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                  ->orWhere('to_branch_id', $branchId);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getInTransitTransfers(?string $branchId = null, int $perPage = 15)
    {
        $query = $this->model->where('status', 'in_transit')
            ->with(['fromBranch', 'toBranch', 'productVariant.product', 'createdBy', 'approvedBy']);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                  ->orWhere('to_branch_id', $branchId);
            });
        }

        return $query->orderBy('transfer_date', 'desc')->paginate($perPage);
    }

    public function getTransfersByDateRange(string $startDate, string $endDate, ?string $branchId = null)
    {
        $query = $this->model->whereBetween('transfer_date', [$startDate, $endDate])
            ->with(['fromBranch', 'toBranch', 'productVariant.product']);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                  ->orWhere('to_branch_id', $branchId);
            });
        }

        return $query->orderBy('transfer_date', 'desc')->get();
    }
}
