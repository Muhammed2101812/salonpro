<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * Create a new repository instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records.
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Find a record by ID.
     */
    public function find(string $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find a record by ID or fail.
     */
    public function findOrFail(string $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Find records by criteria.
     */
    public function findBy(array $criteria, array $columns = ['*']): Collection
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get($columns);
    }

    /**
     * Find first record by criteria.
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first($columns);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record.
     */
    public function update(string $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);

        return $record;
    }

    /**
     * Delete a record.
     */
    public function delete(string $id): bool
    {
        $record = $this->findOrFail($id);

        return (bool) $record->delete();
    }

    /**
     * Force delete a record.
     */
    public function forceDelete(string $id): bool
    {
        $record = $this->model->withTrashed()->findOrFail($id);

        return (bool) $record->forceDelete();
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore(string $id): bool
    {
        $record = $this->model->withTrashed()->findOrFail($id);

        return (bool) $record->restore();
    }
}
