<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServiceRequirementRepositoryInterface;

class ServiceRequirementService extends BaseService
{
    public function __construct(ServiceRequirementRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get requirements for a service
     */
    public function getServiceRequirements(string $serviceId): mixed
    {
        return $this->repository->query()
            ->where('service_id', $serviceId)
            ->with(['service', 'product'])
            ->get();
    }

    /**
     * Get mandatory requirements for a service
     */
    public function getMandatoryRequirements(string $serviceId): mixed
    {
        return $this->repository->query()
            ->where('service_id', $serviceId)
            ->where('is_mandatory', true)
            ->with(['service', 'product'])
            ->get();
    }

    /**
     * Get requirements by type
     */
    public function getRequirementsByType(string $serviceId, string $type): mixed
    {
        return $this->repository->query()
            ->where('service_id', $serviceId)
            ->where('requirement_type', $type)
            ->with(['service', 'product'])
            ->get();
    }

    /**
     * Get product requirements
     */
    public function getProductRequirements(string $serviceId): mixed
    {
        return $this->getRequirementsByType($serviceId, 'product');
    }

    /**
     * Get equipment requirements
     */
    public function getEquipmentRequirements(string $serviceId): mixed
    {
        return $this->getRequirementsByType($serviceId, 'equipment');
    }

    /**
     * Get skill requirements
     */
    public function getSkillRequirements(string $serviceId): mixed
    {
        return $this->getRequirementsByType($serviceId, 'skill');
    }

    /**
     * Check if all mandatory requirements are met
     */
    public function areMandatoryRequirementsMet(string $serviceId): bool
    {
        $mandatoryRequirements = $this->getMandatoryRequirements($serviceId);
        
        // This is a simplified check - you may want to add more complex logic
        return $mandatoryRequirements->every(function ($requirement) {
            if ($requirement->isProduct()) {
                return $requirement->product && $requirement->product->is_active;
            }
            return true;
        });
    }
}
