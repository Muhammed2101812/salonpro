<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface ReportTemplateRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveTemplates();
    public function findByCategory(string $category);
    public function findByCode(string $code);
}
