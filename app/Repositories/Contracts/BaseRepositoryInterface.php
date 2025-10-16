<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find a record by ID.
     */
    public function find(string $id, array $columns = ['*']): ?Model;

    /**
     * Find a record by ID or fail.
     */
    public function findOrFail(string $id, array $columns = ['*']): Model;

    /**
     * Find records by criteria.
     */
    public function findBy(array $criteria, array $columns = ['*']): Collection;

    /**
     * Find first record by criteria.
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update a record.
     */
    public function update(string $id, array $data): Model;

    /**
     * Delete a record.
     */
    public function delete(string $id): bool;

    /**
     * Force delete a record.
     */
    public function forceDelete(string $id): bool;

    /**
     * Restore a soft-deleted record.
     */
    public function restore(string $id): bool;
}
