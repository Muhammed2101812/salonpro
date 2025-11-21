<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface ReportTemplateServiceInterface extends BaseServiceInterface
{
    /**
     * Get active templates.
     */
    public function getActive(): mixed;

    /**
     * Find by category.
     */
    public function getByCategory(string $category): mixed;

    /**
     * Find by template code.
     */
    public function findByCode(string $code): mixed;

    /**
     * Get system templates.
     */
    public function getSystemTemplates(): mixed;

    /**
     * Get user templates.
     */
    public function getUserTemplates(): mixed;

    /**
     * Activate template.
     */
    public function activate(string $id): mixed;

    /**
     * Deactivate template.
     */
    public function deactivate(string $id): mixed;
}
