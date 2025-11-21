<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductVariantServiceInterface;
use Illuminate\Support\Facades\DB;

class ProductVariantService implements ProductVariantServiceInterface
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function createVariant(string $productId, array $data)
    {
        $product = $this->productRepository->findOrFail($productId);

        return DB::transaction(function () use ($productId, $data) {
            $variantData = [
                'product_id' => $productId,
                'sku' => $data['sku'] ?? $this->generateSku(),
                'barcode' => $data['barcode'] ?? null,
                'variant_name' => $data['variant_name'],
                'attributes' => $data['attributes'] ?? null,
                'price' => $data['price'],
                'cost_price' => $data['cost_price'] ?? 0,
                'stock_quantity' => $data['stock_quantity'] ?? 0,
                'reorder_level' => $data['reorder_level'] ?? 10,
                'is_active' => $data['is_active'] ?? true,
            ];

            return $this->productVariantRepository->create($variantData);
        });
    }

    public function updateVariant(string $id, array $data)
    {
        $variant = $this->productVariantRepository->findOrFail($id);

        return $this->productVariantRepository->update($id, $data);
    }

    public function deleteVariant(string $id)
    {
        $variant = $this->productVariantRepository->findOrFail($id);

        if ($variant->stock_quantity > 0) {
            throw new \Exception('Cannot delete variant with existing stock. Please transfer or adjust stock first.');
        }

        return $this->productVariantRepository->delete($id);
    }

    public function getProductVariants(string $productId)
    {
        return $this->productVariantRepository->findByProduct($productId);
    }

    public function findBySku(string $sku)
    {
        return $this->productVariantRepository->findBySku($sku);
    }

    public function findByBarcode(string $barcode)
    {
        return $this->productVariantRepository->findByBarcode($barcode);
    }

    public function getLowStockVariants(int $threshold = 10)
    {
        return $this->productVariantRepository->getLowStockVariants($threshold);
    }

    public function updateStock(string $id, int $quantity, string $type = 'set')
    {
        $variant = $this->productVariantRepository->findOrFail($id);

        return DB::transaction(function () use ($id, $variant, $quantity, $type) {
            $newQuantity = match($type) {
                'add' => $variant->stock_quantity + $quantity,
                'subtract' => $variant->stock_quantity - $quantity,
                'set' => $quantity,
                default => throw new \Exception("Invalid stock update type: {$type}"),
            };

            if ($newQuantity < 0) {
                throw new \Exception('Stock quantity cannot be negative');
            }

            return $this->productVariantRepository->update($id, [
                'stock_quantity' => $newQuantity,
            ]);
        });
    }

    public function checkStock(string $id, int $quantity): bool
    {
        $variant = $this->productVariantRepository->findOrFail($id);

        return $variant->stock_quantity >= $quantity;
    }

    private function generateSku(): string
    {
        $prefix = 'VAR';
        $random = strtoupper(substr(uniqid(), -8));

        return "{$prefix}-{$random}";
    }
}
