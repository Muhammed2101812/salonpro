<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServicePricingRuleRepositoryInterface;

class ServicePricingRuleService extends BaseService
{
    public function __construct(ServicePricingRuleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get active pricing rules for a service
     */
    public function getActiveRulesForService(string $serviceId): mixed
    {
        return $this->repository->query()
            ->where('service_id', $serviceId)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(fn($rule) => $rule->isCurrentlyValid());
    }

    /**
     * Calculate price with applicable rules
     */
    public function calculatePriceWithRules(string $serviceId, float $basePrice): float
    {
        $rules = $this->getActiveRulesForService($serviceId);
        
        if ($rules->isEmpty()) {
            return $basePrice;
        }

        // Apply the highest priority rule
        $highestPriorityRule = $rules->first();
        
        return $highestPriorityRule->calculateAdjustedPrice($basePrice);
    }

    /**
     * Get rules by type
     */
    public function getRulesByType(string $type): mixed
    {
        return $this->repository->query()
            ->where('rule_type', $type)
            ->where('is_active', true)
            ->with('service')
            ->get();
    }

    /**
     * Get currently valid rules
     */
    public function getCurrentlyValidRules(): mixed
    {
        $now = now();
        
        return $this->repository->query()
            ->where('is_active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $now);
            })
            ->with('service')
            ->orderBy('priority', 'desc')
            ->get();
    }
}
