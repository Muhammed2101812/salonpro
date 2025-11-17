<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\KpiDefinitionRepositoryInterface;
use App\Services\Contracts\KpiDefinitionServiceInterface;
use Illuminate\Support\Facades\DB;

class KpiDefinitionService extends BaseService implements KpiDefinitionServiceInterface
{
    public function __construct(
        protected KpiDefinitionRepositoryInterface $kpiDefinitionRepository
    ) {
        parent::__construct($kpiDefinitionRepository);
    }

    public function findByCode(string $code): mixed
    {
        $kpi = $this->kpiDefinitionRepository->findByCode($code);

        if (!$kpi) {
            throw new \RuntimeException("KPI with code '{$code}' not found");
        }

        return $kpi;
    }

    public function getActive(): mixed
    {
        return $this->kpiDefinitionRepository->getActive();
    }

    public function getByCategory(string $category): mixed
    {
        return $this->kpiDefinitionRepository->getByCategory($category);
    }

    public function getByFrequency(string $frequency): mixed
    {
        return $this->kpiDefinitionRepository->getByFrequency($frequency);
    }

    public function activate(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            return $this->kpiDefinitionRepository->update($id, ['is_active' => true]);
        });
    }

    public function deactivate(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            return $this->kpiDefinitionRepository->update($id, ['is_active' => false]);
        });
    }
}
