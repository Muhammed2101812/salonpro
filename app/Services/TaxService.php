<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\TaxRateRepositoryInterface;
use App\Services\Contracts\TaxServiceInterface;

class TaxService implements TaxServiceInterface
{
    public function __construct(
        private TaxRateRepositoryInterface $taxRateRepository
    ) {}

    public function calculateTax(float $amount, ?string $taxRateId = null, ?string $date = null): array
    {
        $date = $date ?? now()->toDateString();

        if ($taxRateId) {
            $taxRate = $this->taxRateRepository->findOrFail($taxRateId);
        } else {
            $taxRate = $this->taxRateRepository->getEffectiveTaxRate($date);
        }

        if (!$taxRate) {
            return [
                'subtotal' => $amount,
                'tax_amount' => 0,
                'tax_rate' => 0,
                'total' => $amount,
            ];
        }

        $taxAmount = round(($amount * $taxRate->rate) / 100, 2);

        return [
            'subtotal' => $amount,
            'tax_amount' => $taxAmount,
            'tax_rate' => $taxRate->rate,
            'tax_name' => $taxRate->name,
            'total' => $amount + $taxAmount,
        ];
    }

    public function getActiveTaxRates()
    {
        return $this->taxRateRepository->getActiveTaxRates();
    }

    public function getEffectiveTaxRate(?string $date = null)
    {
        $date = $date ?? now()->toDateString();

        return $this->taxRateRepository->getEffectiveTaxRate($date);
    }

    public function getTaxBreakdown(float $amount, array $items = []): array
    {
        $breakdown = [];
        $totalTax = 0;

        if (empty($items)) {
            // Calculate tax for total amount
            $calculation = $this->calculateTax($amount);
            $totalTax = $calculation['tax_amount'];
            $breakdown[] = $calculation;
        } else {
            // Calculate tax for each item
            foreach ($items as $item) {
                $itemAmount = $item['price'] * $item['quantity'];
                $calculation = $this->calculateTax(
                    $itemAmount,
                    $item['tax_rate_id'] ?? null
                );

                $calculation['item'] = $item;
                $breakdown[] = $calculation;
                $totalTax += $calculation['tax_amount'];
            }
        }

        return [
            'items' => $breakdown,
            'total_tax' => round($totalTax, 2),
            'subtotal' => $amount,
            'grand_total' => round($amount + $totalTax, 2),
        ];
    }
}
