<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServicePriceHistoryRepositoryInterface;

class ServicePriceHistoryService extends BaseService
{
    public function __construct(ServicePriceHistoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Record a price change
     */
    public function recordPriceChange(
        string $serviceId,
        float $oldPrice,
        float $newPrice,
        ?string $changedBy = null,
        ?string $reason = null
    ): mixed {
        $priceChange = $newPrice - $oldPrice;
        $priceChangePercentage = $oldPrice > 0 
            ? round(($priceChange / $oldPrice) * 100, 2) 
            : 0;

        return $this->create([
            'service_id' => $serviceId,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'price_change' => $priceChange,
            'price_change_percentage' => $priceChangePercentage,
            'changed_by' => $changedBy,
            'reason' => $reason,
            'changed_at' => now(),
        ]);
    }

    /**
     * Get price history for a service
     */
    public function getServicePriceHistory(string $serviceId): mixed
    {
        return $this->repository->query()
            ->where('service_id', $serviceId)
            ->with(['service', 'changedBy'])
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    /**
     * Get recent price changes
     */
    public function getRecentPriceChanges(int $limit = 10): mixed
    {
        return $this->repository->query()
            ->with(['service', 'changedBy'])
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
