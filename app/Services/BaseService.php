<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    /**
     * The repository instance.
     */
    protected BaseRepositoryInterface $repository;

    /**
     * Create a new service instance.
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all records.
     */
    public function getAll(array $columns = ['*']): Collection
    {
        return $this->repository->all($columns);
    }

    /**
     * Get paginated records.
     */
    public function getPaginated(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $columns);
    }

    /**
     * Find a record by ID.
     */
    public function findById(string $id, array $columns = ['*']): ?Model
    {
        return $this->repository->find($id, $columns);
    }

    /**
     * Find a record by ID or fail.
     */
    public function findByIdOrFail(string $id, array $columns = ['*']): Model
    {
        return $this->repository->findOrFail($id, $columns);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    /**
     * Update a record.
     */
    public function update(string $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->repository->update($id, $data);
        });
    }

    /**
     * Delete a record.
     */
    public function delete(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }

    /**
     * Force delete a record.
     */
    public function forceDelete(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->forceDelete($id);
        });
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->restore($id);
        });
    }
}
