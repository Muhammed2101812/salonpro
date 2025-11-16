<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\TaxRate;
use App\Repositories\Contracts\TaxRateRepositoryInterface;

class TaxRateRepository extends BaseRepository implements TaxRateRepositoryInterface
{
    public function __construct(TaxRate $model)
    {
        parent::__construct($model);
    }

    public function getActiveTaxRates()
    {
        return $this->model->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getEffectiveTaxRate(string $date = null)
    {
        $date = $date ?? now()->format('Y-m-d');
        
        return $this->model->where('is_active', true)
            ->where(function ($query) use ($date) {
                $query->where('effective_from', '<=', $date)
                    ->where(function ($q) use ($date) {
                        $q->whereNull('effective_until')
                            ->orWhere('effective_until', '>=', $date);
                    });
            })
            ->first();
    }
}
