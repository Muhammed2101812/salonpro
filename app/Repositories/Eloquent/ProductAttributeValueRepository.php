<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ProductAttributeValue;
use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;
use Illuminate\Support\Collection;

class ProductAttributeValueRepository extends BaseRepository implements ProductAttributeValueRepositoryInterface
{
    public function __construct(ProductAttributeValue $model)
    {
        parent::__construct($model);
    }

    public function findByProduct(string $productId): Collection
    {
        return $this->model->with(['product', 'attribute'])
            ->where('product_id', $productId)
            ->get();
    }

    public function findByAttributeValue(string $attributeId, string $value): Collection
    {
        return $this->model->with(['product', 'attribute'])
            ->where('attribute_id', $attributeId)
            ->where('attribute_value', $value)
            ->get();
    }

    public function findProductAttribute(string $productId, string $attributeId): mixed
    {
        return $this->model->with(['product', 'attribute'])
            ->where('product_id', $productId)
            ->where('attribute_id', $attributeId)
            ->first();
    }

    public function setProductAttribute(string $productId, string $attributeId, string $value): mixed
    {
        return $this->model->updateOrCreate(
            [
                'product_id' => $productId,
                'attribute_id' => $attributeId,
            ],
            [
                'attribute_value' => $value,
            ]
        );
    }

    public function deleteProductAttribute(string $productId, string $attributeId): bool
    {
        return (bool) $this->model
            ->where('product_id', $productId)
            ->where('attribute_id', $attributeId)
            ->delete();
    }

    public function getValuesByAttribute(string $attributeId): Collection
    {
        return $this->model->with(['product', 'attribute'])
            ->where('attribute_id', $attributeId)
            ->get();
    }
}
