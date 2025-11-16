<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\Contracts\SupplierServiceInterface;
use Illuminate\Support\Facades\DB;

class SupplierService extends BaseService implements SupplierServiceInterface
{
    public function __construct(
        protected SupplierRepositoryInterface $supplierRepository
    ) {
        parent::__construct($supplierRepository);
    }

    public function getActive(): mixed
    {
        return $this->supplierRepository->findActive();
    }

    public function search(string $query, int $perPage = 15): mixed
    {
        return $this->supplierRepository->search($query, $perPage);
    }

    public function getSupplierStats(string $supplierId): array
    {
        return $this->supplierRepository->getStats($supplierId);
    }

    public function activate(string $id): mixed
    {
        return $this->supplierRepository->update($id, ['is_active' => true]);
    }

    public function deactivate(string $id): mixed
    {
        return $this->supplierRepository->update($id, ['is_active' => false]);
    }
}
