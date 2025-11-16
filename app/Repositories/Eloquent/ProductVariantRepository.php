<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;

class ProductVariantRepository extends BaseRepository implements ProductVariantRepositoryInterface
{
    public function __construct(ProductVariant $model)
    {
        parent::__construct($model);
    }

    public function findByProduct(string $productId)
    {
        return $this->model->where('product_id', $productId)
            ->where('is_active', true)
            ->orderBy('variant_name')
            ->get();
    }

    public function findBySku(string $sku)
    {
        return $this->model->where('sku', $sku)->with('product')->first();
    }

    public function findByBarcode(string $barcode)
    {
        return $this->model->where('barcode', $barcode)->with('product')->first();
    }

    public function getLowStockVariants(int $threshold = 10)
    {
        return $this->model->where('stock_quantity', '<=', $threshold)
            ->where('is_active', true)
            ->with('product')
            ->orderBy('stock_quantity')
            ->get();
    }
}
