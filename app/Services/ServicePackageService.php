<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServicePackageRepositoryInterface;

class ServicePackageService extends BaseService
{
    public function __construct(ServicePackageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Calculate and update the final price of a package
     */
    public function calculateFinalPrice(string $id): float
    {
        $package = $this->findByIdOrFail($id);
        $finalPrice = $package->calculateFinalPrice();
        
        $this->update($id, ['final_price' => $finalPrice]);
        
        return $finalPrice;
    }

    /**
     * Get active packages for a branch
     */
    public function getActivePackages(?string $branchId = null): mixed
    {
        $query = $this->repository->query()->where('is_active', true);
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        return $query->with(['services', 'branch'])->get();
    }

    /**
     * Attach services to a package
     */
    public function attachServices(string $packageId, array $services): void
    {
        $package = $this->findByIdOrFail($packageId);
        
        $syncData = [];
        foreach ($services as $service) {
            $syncData[$service['service_id']] = [
                'quantity' => $service['quantity'] ?? 1,
                'price_override' => $service['price_override'] ?? null,
            ];
        }
        
        $package->services()->sync($syncData);
    }
}
