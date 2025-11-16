<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProductAttributeValueRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get attribute values by product.
     */
    public function findByProduct(string $productId): Collection;

    /**
     * Get products by attribute value.
     */
    public function findByAttributeValue(string $attributeId, string $value): Collection;

    /**
     * Get attribute value for product and attribute.
     */
    public function findProductAttribute(string $productId, string $attributeId): mixed;

    /**
     * Set attribute value for product.
     */
    public function setProductAttribute(string $productId, string $attributeId, string $value): mixed;

    /**
     * Delete product attribute.
     */
    public function deleteProductAttribute(string $productId, string $attributeId): bool;

    /**
     * Get all values for an attribute.
     */
    public function getValuesByAttribute(string $attributeId): Collection;
}
