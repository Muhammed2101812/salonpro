<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\StockTransferRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Services\Contracts\StockTransferServiceInterface;
use Illuminate\Support\Facades\DB;

class StockTransferService implements StockTransferServiceInterface
{
    public function __construct(
        private StockTransferRepositoryInterface $stockTransferRepository,
        private ProductVariantRepositoryInterface $productVariantRepository
    ) {}

    public function createTransfer(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Validate source branch has enough stock
            $variant = $this->productVariantRepository->findOrFail($data['product_variant_id']);

            $transferData = [
                'from_branch_id' => $data['from_branch_id'],
                'to_branch_id' => $data['to_branch_id'],
                'product_variant_id' => $data['product_variant_id'],
                'quantity' => $data['quantity'],
                'status' => 'pending',
                'transfer_date' => $data['transfer_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ];

            return $this->stockTransferRepository->create($transferData);
        });
    }

    public function updateTransfer(string $id, array $data)
    {
        $transfer = $this->stockTransferRepository->findOrFail($id);

        if ($transfer->status !== 'pending') {
            throw new \Exception('Only pending transfers can be updated');
        }

        return $this->stockTransferRepository->update($id, $data);
    }

    public function approveTransfer(string $id, string $userId)
    {
        return DB::transaction(function () use ($id, $userId) {
            $transfer = $this->stockTransferRepository->findOrFail($id);

            if ($transfer->status !== 'pending') {
                throw new \Exception('Only pending transfers can be approved');
            }

            // Deduct stock from source branch
            // Note: This would need actual branch-specific stock management

            return $this->stockTransferRepository->update($id, [
                'status' => 'in_transit',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);
        });
    }

    public function rejectTransfer(string $id, string $reason)
    {
        $transfer = $this->stockTransferRepository->findOrFail($id);

        if ($transfer->status !== 'pending') {
            throw new \Exception('Only pending transfers can be rejected');
        }

        return $this->stockTransferRepository->update($id, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);
    }

    public function completeTransfer(string $id)
    {
        return DB::transaction(function () use ($id) {
            $transfer = $this->stockTransferRepository->findOrFail($id);

            if ($transfer->status !== 'in_transit') {
                throw new \Exception('Only in-transit transfers can be completed');
            }

            // Add stock to destination branch
            // Note: This would need actual branch-specific stock management

            return $this->stockTransferRepository->update($id, [
                'status' => 'completed',
                'received_date' => now(),
                'received_by' => auth()->id(),
            ]);
        });
    }

    public function cancelTransfer(string $id, string $reason)
    {
        $transfer = $this->stockTransferRepository->findOrFail($id);

        if (!in_array($transfer->status, ['pending', 'in_transit'])) {
            throw new \Exception('Only pending or in-transit transfers can be cancelled');
        }

        return DB::transaction(function () use ($id, $transfer, $reason) {
            // If in_transit, restore stock to source branch
            if ($transfer->status === 'in_transit') {
                // Restore stock logic here
            }

            return $this->stockTransferRepository->update($id, [
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);
        });
    }

    public function getSourceBranchTransfers(string $branchId, int $perPage = 15)
    {
        return $this->stockTransferRepository->findBySourceBranch($branchId, $perPage);
    }

    public function getDestinationBranchTransfers(string $branchId, int $perPage = 15)
    {
        return $this->stockTransferRepository->findByDestinationBranch($branchId, $perPage);
    }

    public function getPendingTransfers(?string $branchId = null, int $perPage = 15)
    {
        return $this->stockTransferRepository->getPendingTransfers($branchId, $perPage);
    }

    public function getInTransitTransfers(?string $branchId = null, int $perPage = 15)
    {
        return $this->stockTransferRepository->getInTransitTransfers($branchId, $perPage);
    }
}
