<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServiceTemplateRepositoryInterface;

class ServiceTemplateService extends BaseService
{
    public function __construct(ServiceTemplateRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create a service from a template
     */
    public function createServiceFromTemplate(string $templateId, array $overrides = []): array
    {
        $template = $this->findByIdOrFail($templateId);
        
        return $template->createService($overrides);
    }

    /**
     * Get all non-system templates
     */
    public function getNonSystemTemplates(): mixed
    {
        return $this->repository->query()
            ->where('is_system', false)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get system templates
     */
    public function getSystemTemplates(): mixed
    {
        return $this->repository->query()
            ->where('is_system', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Delete template (only if not system template)
     */
    public function deleteTemplate(string $id): bool
    {
        $template = $this->findByIdOrFail($id);
        
        if (!$template->canBeDeleted()) {
            throw new \Exception('Sistem şablonları silinemez.');
        }
        
        return $this->delete($id);
    }

    /**
     * Get template by name
     */
    public function getByName(string $name): mixed
    {
        return $this->repository->query()
            ->where('name', $name)
            ->first();
    }
}
