<?php

namespace App\Services;

use App\Models\StockAlert;
use App\Repositories\Contracts\StockAlertRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class StockAlertService
{
    public function __construct(
        private StockAlertRepositoryInterface $stockAlertRepository
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->stockAlertRepository->getAllPaginated($perPage);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockAlertRepository->getAll();
    }

    public function findById(int $id): ?StockAlert
    {
        return $this->stockAlertRepository->findById($id);
    }

    public function create(array $data): StockAlert
    {
        return $this->stockAlertRepository->create($data);
    }

    public function update(int $id, array $data): StockAlert
    {
        $alert = $this->stockAlertRepository->findById($id);
        
        if (!$alert) {
            throw new \Exception('Stok uyarısı bulunamadı');
        }

        $this->stockAlertRepository->update($alert, $data);
        
        return $alert->fresh();
    }

    public function delete(int $id): bool
    {
        $alert = $this->stockAlertRepository->findById($id);
        
        if (!$alert) {
            throw new \Exception('Stok uyarısı bulunamadı');
        }

        return $this->stockAlertRepository->delete($alert);
    }

    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockAlertRepository->getActive();
    }

    public function getByProduct(int $productId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockAlertRepository->getByProduct($productId);
    }

    public function markAsResolved(int $id): StockAlert
    {
        $alert = $this->stockAlertRepository->findById($id);
        
        if (!$alert) {
            throw new \Exception('Stok uyarısı bulunamadı');
        }

        $this->stockAlertRepository->markAsResolved($alert);

        return $alert->fresh();
    }

    public function getUnresolved(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockAlertRepository->getUnresolved();
    }

    public function checkAndCreateAlerts(): array
    {
        $createdAlerts = [];
        
        // Get products with low stock that don't have unresolved alerts
        $products = \App\Models\Product::whereRaw('quantity <= min_quantity')
            ->whereDoesntHave('stockAlerts', function ($query) {
                $query->where('is_resolved', false);
            })
            ->get();

        foreach ($products as $product) {
            $alert = $this->create([
                'product_id' => $product->id,
                'branch_id' => $product->branch_id,
                'alert_type' => 'low_stock',
                'threshold' => $product->min_quantity,
                'current_quantity' => $product->quantity,
                'message' => "{$product->name} ürünü minimum stok seviyesinin altında ({$product->quantity}/{$product->min_quantity})",
                'is_resolved' => false,
            ]);

            $createdAlerts[] = $alert;
        }

        return $createdAlerts;
    }
}
