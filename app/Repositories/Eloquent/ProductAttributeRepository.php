<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ProductAttribute;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use Illuminate\Support\Collection;

class ProductAttributeRepository extends BaseRepository implements ProductAttributeRepositoryInterface
{
    public function __construct(ProductAttribute $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): mixed
    {
        return $this->model->with('values')
            ->where('attribute_code', $code)
            ->first();
    }

    public function getFilterable(): Collection
    {
        return $this->model->with('values')
            ->where('is_filterable', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getRequired(): Collection
    {
        return $this->model->with('values')
            ->where('is_required', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getByType(string $type): Collection
    {
        return $this->model->with('values')
            ->where('attribute_type', $type)
            ->orderBy('sort_order')
            ->get();
    }

    public function getAllSorted(): Collection
    {
        return $this->model->with('values')
            ->orderBy('sort_order')
            ->orderBy('attribute_name')
            ->get();
    }
}
