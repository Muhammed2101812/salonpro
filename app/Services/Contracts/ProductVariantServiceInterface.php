<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface ProductVariantServiceInterface
{
    public function createVariant(string $productId, array $data);
    public function updateVariant(string $id, array $data);
    public function deleteVariant(string $id);
    public function getProductVariants(string $productId);
    public function findBySku(string $sku);
    public function findByBarcode(string $barcode);
    public function getLowStockVariants(int $threshold = 10);
    public function updateStock(string $id, int $quantity, string $type = 'set');
    public function checkStock(string $id, int $quantity): bool;
}
