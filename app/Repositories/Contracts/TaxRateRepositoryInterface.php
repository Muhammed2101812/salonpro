<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface TaxRateRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveTaxRates();
    public function getEffectiveTaxRate(string $date = null);
}
