<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface ProductAttributeServiceInterface extends BaseServiceInterface
{
    /**
     * Find attribute by code.
     */
    public function findByCode(string $code): mixed;

    /**
     * Get filterable attributes.
     */
    public function getFilterable(): mixed;

    /**
     * Get required attributes.
     */
    public function getRequired(): mixed;

    /**
     * Get all sorted.
     */
    public function getAllSorted(): mixed;
}
