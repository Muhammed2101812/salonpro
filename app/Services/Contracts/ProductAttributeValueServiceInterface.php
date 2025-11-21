<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface ProductAttributeValueServiceInterface extends BaseServiceInterface
{
    /**
     * Get attributes for product.
     */
    public function getProductAttributes(string $productId): mixed;

    /**
     * Get products with attribute value.
     */
    public function getProductsByAttributeValue(string $attributeId, string $value): mixed;

    /**
     * Set product attribute.
     */
    public function setProductAttribute(string $productId, string $attributeId, string $value): mixed;

    /**
     * Delete product attribute.
     */
    public function deleteProductAttribute(string $productId, string $attributeId): bool;

    /**
     * Bulk set product attributes.
     */
    public function bulkSetAttributes(string $productId, array $attributes): array;
}
