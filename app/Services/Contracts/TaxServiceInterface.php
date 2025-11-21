<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface TaxServiceInterface
{
    public function calculateTax(float $amount, ?string $taxRateId = null, ?string $date = null): array;
    public function getActiveTaxRates();
    public function getEffectiveTaxRate(?string $date = null);
    public function getTaxBreakdown(float $amount, array $items = []): array;
}
