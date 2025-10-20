<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServiceAddonRepositoryInterface;

class ServiceAddonService extends BaseService
{
    public function __construct(ServiceAddonRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get active addons for a branch
     */
    public function getActiveAddons(?string $branchId = null): mixed
    {
        $query = $this->repository->query()->where('is_active', true);
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        return $query->with('branch')->get();
    }

    /**
     * Get available addons (active and not soft deleted)
     */
    public function getAvailableAddons(?string $branchId = null): mixed
    {
        $query = $this->repository->query()
            ->where('is_active', true)
            ->whereNull('deleted_at');
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        return $query->with('branch')->get();
    }

    /**
     * Attach addon to services
     */
    public function attachToServices(string $addonId, array $serviceIds, ?float $priceOverride = null): void
    {
        $addon = $this->findByIdOrFail($addonId);
        
        $syncData = [];
        foreach ($serviceIds as $serviceId) {
            $syncData[$serviceId] = [
                'price_override' => $priceOverride,
            ];
        }
        
        $addon->services()->sync($syncData);
    }

    /**
     * Get addons by service
     */
    public function getAddonsByService(string $serviceId): mixed
    {
        return $this->repository->query()
            ->whereHas('services', function($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->where('is_active', true)
            ->with('branch')
            ->get();
    }
}
