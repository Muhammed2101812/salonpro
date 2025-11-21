<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ReportTemplateRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active templates.
     */
    public function getActiveTemplates(): Collection;

    /**
     * Find by category.
     */
    public function findByCategory(string $category): Collection;

    /**
     * Find by template code.
     */
    public function findByCode(string $code): mixed;

    /**
     * Get system templates.
     */
    public function getSystemTemplates(): Collection;

    /**
     * Get user templates.
     */
    public function getUserTemplates(): Collection;

    /**
     * Get templates by creator.
     */
    public function getByCreator(string $userId): Collection;
}
