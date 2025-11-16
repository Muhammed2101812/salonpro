<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find active suppliers.
     */
    public function findActive(): Collection;

    /**
     * Find suppliers by city.
     */
    public function findByCity(string $city, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find suppliers by country.
     */
    public function findByCountry(string $country, int $perPage = 15): LengthAwarePaginator;

    /**
     * Search suppliers by name or contact person.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get supplier statistics.
     */
    public function getStats(string $supplierId): array;
}
