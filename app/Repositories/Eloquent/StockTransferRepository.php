<?php

namespace App\Repositories\Eloquent;

use App\Models\StockTransfer;
use App\Repositories\Contracts\StockTransferRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class StockTransferRepository implements StockTransferRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return StockTransfer::with(['product', 'fromBranch', 'toBranch', 'requestedBy', 'approvedBy'])
            ->orderBy('transfer_date', 'desc')
            ->paginate($perPage);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return StockTransfer::with(['product', 'fromBranch', 'toBranch', 'requestedBy', 'approvedBy'])
            ->orderBy('transfer_date', 'desc')
            ->get();
    }

    public function findById(int $id): ?StockTransfer
    {
        return StockTransfer::with(['product', 'fromBranch', 'toBranch', 'requestedBy', 'approvedBy'])
            ->find($id);
    }

    public function create(array $data): StockTransfer
    {
        return StockTransfer::create($data);
    }

    public function update(StockTransfer $transfer, array $data): bool
    {
        return $transfer->update($data);
    }

    public function delete(StockTransfer $transfer): bool
    {
        return $transfer->delete();
    }

    public function getByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return StockTransfer::with(['product', 'fromBranch', 'toBranch', 'requestedBy', 'approvedBy'])
            ->where('status', $status)
            ->orderBy('transfer_date', 'desc')
            ->get();
    }

    public function getFromBranch(int $branchId): \Illuminate\Database\Eloquent\Collection
    {
        return StockTransfer::with(['product', 'toBranch', 'requestedBy', 'approvedBy'])
            ->where('from_branch_id', $branchId)
            ->orderBy('transfer_date', 'desc')
            ->get();
    }

    public function getToBranch(int $branchId): \Illuminate\Database\Eloquent\Collection
    {
        return StockTransfer::with(['product', 'fromBranch', 'requestedBy', 'approvedBy'])
            ->where('to_branch_id', $branchId)
            ->orderBy('transfer_date', 'desc')
            ->get();
    }

    public function updateStatus(StockTransfer $transfer, string $status): bool
    {
        return $transfer->update(['status' => $status]);
    }

    public function getPending(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getByStatus('pending');
    }
}
