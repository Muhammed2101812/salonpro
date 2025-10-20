<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function __construct(
        private SupplierRepositoryInterface $supplierRepository
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->supplierRepository->getAllPaginated($perPage);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->supplierRepository->getAll();
    }

    public function findById(int $id): ?Supplier
    {
        return $this->supplierRepository->findById($id);
    }

    public function create(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    public function update(int $id, array $data): Supplier
    {
        $supplier = $this->supplierRepository->findById($id);
        
        if (!$supplier) {
            throw new \Exception('Tedarikçi bulunamadı');
        }

        $this->supplierRepository->update($supplier, $data);
        
        return $supplier->fresh();
    }

    public function delete(int $id): bool
    {
        $supplier = $this->supplierRepository->findById($id);
        
        if (!$supplier) {
            throw new \Exception('Tedarikçi bulunamadı');
        }

        // Check if supplier has any purchase orders
        if ($supplier->purchaseOrders()->count() > 0) {
            throw new \Exception('Bu tedarikçinin satın alma siparişleri bulunmaktadır. Silinemez.');
        }

        return $this->supplierRepository->delete($supplier);
    }

    public function search(string $query): \Illuminate\Database\Eloquent\Collection
    {
        return $this->supplierRepository->search($query);
    }

    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->supplierRepository->getActive();
    }

    public function getWithLowStock(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->supplierRepository->getWithLowStock();
    }

    public function toggleStatus(int $id): Supplier
    {
        $supplier = $this->supplierRepository->findById($id);
        
        if (!$supplier) {
            throw new \Exception('Tedarikçi bulunamadı');
        }

        $this->supplierRepository->update($supplier, [
            'is_active' => !$supplier->is_active
        ]);

        return $supplier->fresh();
    }
}
