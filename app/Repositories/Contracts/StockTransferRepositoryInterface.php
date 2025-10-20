<?php

namespace App\Repositories\Contracts;

use App\Models\StockTransfer;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockTransferRepositoryInterface
{
    /**
     * Get all stock transfers with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all stock transfers
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Find a stock transfer by ID
     */
    public function findById(int $id): ?StockTransfer;

    /**
     * Create a new stock transfer
     */
    public function create(array $data): StockTransfer;

    /**
     * Update a stock transfer
     */
    public function update(StockTransfer $transfer, array $data): bool;

    /**
     * Delete a stock transfer
     */
    public function delete(StockTransfer $transfer): bool;

    /**
     * Get transfers by status
     */
    public function getByStatus(string $status): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get transfers from a specific branch
     */
    public function getFromBranch(int $branchId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get transfers to a specific branch
     */
    public function getToBranch(int $branchId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Update transfer status
     */
    public function updateStatus(StockTransfer $transfer, string $status): bool;

    /**
     * Get pending transfers
     */
    public function getPending(): \Illuminate\Database\Eloquent\Collection;
}
