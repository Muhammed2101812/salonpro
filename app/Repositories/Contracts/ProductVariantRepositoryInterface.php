<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface ProductVariantRepositoryInterface extends BaseRepositoryInterface
{
    public function findByProduct(string $productId);
    public function findBySku(string $sku);
    public function findByBarcode(string $barcode);
    public function getLowStockVariants(int $threshold = 10);
}
