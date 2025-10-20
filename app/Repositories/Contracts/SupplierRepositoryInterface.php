<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    /**
     * Get all suppliers for the current branch with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all suppliers for the current branch
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Find a supplier by ID
     */
    public function findById(int $id): ?Supplier;

    /**
     * Create a new supplier
     */
    public function create(array $data): Supplier;

    /**
     * Update a supplier
     */
    public function update(Supplier $supplier, array $data): bool;

    /**
     * Delete a supplier
     */
    public function delete(Supplier $supplier): bool;

    /**
     * Search suppliers by name, email, or phone
     */
    public function search(string $query): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get active suppliers only
     */
    public function getActive(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get suppliers with low stock products
     */
    public function getWithLowStock(): \Illuminate\Database\Eloquent\Collection;
}
