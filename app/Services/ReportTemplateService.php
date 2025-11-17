<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ReportTemplateRepositoryInterface;
use App\Services\Contracts\ReportTemplateServiceInterface;
use Illuminate\Support\Facades\DB;

class ReportTemplateService extends BaseService implements ReportTemplateServiceInterface
{
    public function __construct(
        protected ReportTemplateRepositoryInterface $reportTemplateRepository
    ) {
        parent::__construct($reportTemplateRepository);
    }

    public function getActive(): mixed
    {
        return $this->reportTemplateRepository->getActiveTemplates();
    }

    public function getByCategory(string $category): mixed
    {
        return $this->reportTemplateRepository->findByCategory($category);
    }

    public function findByCode(string $code): mixed
    {
        $template = $this->reportTemplateRepository->findByCode($code);

        if (!$template) {
            throw new \RuntimeException("Report template with code '{$code}' not found");
        }

        return $template;
    }

    public function getSystemTemplates(): mixed
    {
        return $this->reportTemplateRepository->getSystemTemplates();
    }

    public function getUserTemplates(): mixed
    {
        return $this->reportTemplateRepository->getUserTemplates();
    }

    public function activate(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            return $this->reportTemplateRepository->update($id, ['is_active' => true]);
        });
    }

    public function deactivate(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $template = $this->reportTemplateRepository->findOrFail($id);

            if ($template->is_system) {
                throw new \RuntimeException('System templates cannot be deactivated');
            }

            return $this->reportTemplateRepository->update($id, ['is_active' => false]);
        });
    }
}
