<?php

namespace App\Services;

use App\Repositories\Contracts\ProductBundleRepositoryInterface;
use App\Models\ProductBundle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductBundleService
{
    public function __construct(
        protected ProductBundleRepositoryInterface $repository
    ) {}

    public function getAllBundles(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginatedBundles(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getBundleById(int $id): ?ProductBundle
    {
        return $this->repository->findById($id);
    }

    public function createBundle(array $data): ProductBundle
    {
        $data['is_active'] = $data['is_active'] ?? true;
        
        // Calculate total price if not provided
        if (!isset($data['price'])) {
            $data['price'] = $this->calculateBundlePrice($data['items'] ?? []);
        }

        return $this->repository->create($data);
    }

    public function updateBundle(int $id, array $data): ProductBundle
    {
        $bundle = $this->repository->findById($id);
        
        if (!$bundle) {
            throw new \Exception('Ürün paketi bulunamadı.');
        }

        // Recalculate price if items are updated
        if (isset($data['items']) && !isset($data['price'])) {
            $data['price'] = $this->calculateBundlePrice($data['items']);
        }

        return $this->repository->update($id, $data);
    }

    public function deleteBundle(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getBundlesByBranch(int $branchId): Collection
    {
        return $this->repository->findByBranch($branchId);
    }

    public function getActiveBundles(): Collection
    {
        return $this->repository->findActive();
    }

    public function getBundlesByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function activateBundle(int $id): ProductBundle
    {
        return $this->repository->activate($id);
    }

    public function deactivateBundle(int $id): ProductBundle
    {
        return $this->repository->deactivate($id);
    }

    public function checkBundleAvailability(int $id): bool
    {
        return $this->repository->checkAvailability($id);
    }

    public function calculateBundleDiscount(int $id): array
    {
        $bundle = $this->repository->findById($id);
        
        if (!$bundle) {
            throw new \Exception('Ürün paketi bulunamadı.');
        }

        $totalIndividualPrice = 0;
        foreach ($bundle->items as $item) {
            $totalIndividualPrice += ($item->product->price * $item->quantity);
        }

        $discountAmount = $totalIndividualPrice - $bundle->price;
        $discountPercentage = $totalIndividualPrice > 0 
            ? round(($discountAmount / $totalIndividualPrice) * 100, 2) 
            : 0;

        return [
            'individual_total' => round($totalIndividualPrice, 2),
            'bundle_price' => round($bundle->price, 2),
            'discount_amount' => round($discountAmount, 2),
            'discount_percentage' => $discountPercentage,
        ];
    }

    public function sellBundle(int $id, int $quantity = 1): array
    {
        $bundle = $this->repository->findById($id);
        
        if (!$bundle) {
            throw new \Exception('Ürün paketi bulunamadı.');
        }

        if (!$this->checkBundleAvailability($id)) {
            throw new \Exception('Ürün paketi şu anda satışa uygun değil.');
        }

        // Check if all products have sufficient stock
        foreach ($bundle->items as $item) {
            $requiredQuantity = $item->quantity * $quantity;
            if ($item->product->quantity < $requiredQuantity) {
                throw new \Exception("Yetersiz stok: {$item->product->name} (Gerekli: {$requiredQuantity}, Mevcut: {$item->product->quantity})");
            }
        }

        // Deduct stock
        foreach ($bundle->items as $item) {
            $item->product->decrement('quantity', $item->quantity * $quantity);
        }

        return [
            'bundle_id' => $bundle->id,
            'bundle_name' => $bundle->name,
            'quantity' => $quantity,
            'unit_price' => $bundle->price,
            'total_price' => $bundle->price * $quantity,
            'items_sold' => $bundle->items->map(fn($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity * $quantity,
            ]),
        ];
    }

    protected function calculateBundlePrice(array $items): float
    {
        // This is a placeholder - in real implementation, you'd fetch product prices
        // For now, return 0 and let the user specify the price
        return 0;
    }
}
