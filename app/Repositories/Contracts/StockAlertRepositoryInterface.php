<?php

namespace App\Repositories\Contracts;

use App\Models\StockAlert;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockAlertRepositoryInterface
{
    /**
     * Get all stock alerts for the current branch with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all stock alerts for the current branch
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Find a stock alert by ID
     */
    public function findById(int $id): ?StockAlert;

    /**
     * Create a new stock alert
     */
    public function create(array $data): StockAlert;

    /**
     * Update a stock alert
     */
    public function update(StockAlert $alert, array $data): bool;

    /**
     * Delete a stock alert
     */
    public function delete(StockAlert $alert): bool;

    /**
     * Get active alerts only
     */
    public function getActive(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get alerts by product
     */
    public function getByProduct(int $productId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Mark alert as resolved
     */
    public function markAsResolved(StockAlert $alert): bool;

    /**
     * Get unresolved alerts
     */
    public function getUnresolved(): \Illuminate\Database\Eloquent\Collection;
}
