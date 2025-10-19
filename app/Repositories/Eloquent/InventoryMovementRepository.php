<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\InventoryMovement;
use App\Repositories\Contracts\InventoryMovementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryMovementRepository extends BaseRepository implements InventoryMovementRepositoryInterface
{
    public function __construct(InventoryMovement $model)
    {
        parent::__construct($model);
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->with(['product', 'user'])->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with(['product', 'user'])->paginate($perPage, $columns);
    }
}
