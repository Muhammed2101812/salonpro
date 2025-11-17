<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\KpiDefinition;
use App\Repositories\Contracts\KpiDefinitionRepositoryInterface;
use Illuminate\Support\Collection;

class KpiDefinitionRepository extends BaseRepository implements KpiDefinitionRepositoryInterface
{
    public function __construct(KpiDefinition $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): mixed
    {
        return $this->model->with('values')
            ->where('kpi_code', $code)
            ->first();
    }

    public function getActive(): Collection
    {
        return $this->model->with('values')
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('kpi_name')
            ->get();
    }

    public function getByCategory(string $category): Collection
    {
        return $this->model->with('values')
            ->where('category', $category)
            ->where('is_active', true)
            ->orderBy('kpi_name')
            ->get();
    }

    public function getByFrequency(string $frequency): Collection
    {
        return $this->model->with('values')
            ->where('frequency', $frequency)
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('kpi_name')
            ->get();
    }
}
