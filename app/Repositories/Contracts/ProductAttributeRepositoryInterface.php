<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProductAttributeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find by attribute code.
     */
    public function findByCode(string $code): mixed;

    /**
     * Get filterable attributes.
     */
    public function getFilterable(): Collection;

    /**
     * Get required attributes.
     */
    public function getRequired(): Collection;

    /**
     * Get by attribute type.
     */
    public function getByType(string $type): Collection;

    /**
     * Get all sorted by sort_order.
     */
    public function getAllSorted(): Collection;
}
