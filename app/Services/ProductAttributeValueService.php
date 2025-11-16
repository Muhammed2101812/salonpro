<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;
use App\Services\Contracts\ProductAttributeValueServiceInterface;
use Illuminate\Support\Facades\DB;

class ProductAttributeValueService extends BaseService implements ProductAttributeValueServiceInterface
{
    public function __construct(
        protected ProductAttributeValueRepositoryInterface $attributeValueRepository
    ) {
        parent::__construct($attributeValueRepository);
    }

    public function getProductAttributes(string $productId): mixed
    {
        return $this->attributeValueRepository->findByProduct($productId);
    }

    public function getProductsByAttributeValue(string $attributeId, string $value): mixed
    {
        return $this->attributeValueRepository->findByAttributeValue($attributeId, $value);
    }

    public function setProductAttribute(string $productId, string $attributeId, string $value): mixed
    {
        return DB::transaction(function () use ($productId, $attributeId, $value) {
            return $this->attributeValueRepository->setProductAttribute($productId, $attributeId, $value);
        });
    }

    public function deleteProductAttribute(string $productId, string $attributeId): bool
    {
        return DB::transaction(function () use ($productId, $attributeId) {
            return $this->attributeValueRepository->deleteProductAttribute($productId, $attributeId);
        });
    }

    public function bulkSetAttributes(string $productId, array $attributes): array
    {
        return DB::transaction(function () use ($productId, $attributes) {
            $results = [];

            foreach ($attributes as $attributeId => $value) {
                $results[$attributeId] = $this->attributeValueRepository->setProductAttribute(
                    $productId,
                    $attributeId,
                    $value
                );
            }

            return $results;
        });
    }
}
