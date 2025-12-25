<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ReportTemplate;
use App\Repositories\Contracts\ReportTemplateRepositoryInterface;
use Illuminate\Support\Collection;

class ReportTemplateRepository extends BaseRepository implements ReportTemplateRepositoryInterface
{
    public function __construct(ReportTemplate $model)
    {
        parent::__construct($model);
    }

    public function getActiveTemplates(): Collection
    {
        return $this->model->with('creator')
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('template_name')
            ->get();
    }

    public function findByCategory(string $category): Collection
    {
        return $this->model->with('creator')
            ->where('category', $category)
            ->where('is_active', true)
            ->orderBy('template_name')
            ->get();
    }

    public function findByCode(string $code): mixed
    {
        return $this->model->with(['creator', 'schedules', 'executions'])
            ->where('template_code', $code)
            ->first();
    }

    public function getSystemTemplates(): Collection
    {
        return $this->model->with('creator')
            ->where('is_system', true)
            ->orderBy('category')
            ->orderBy('template_name')
            ->get();
    }

    public function getUserTemplates(): Collection
    {
        return $this->model->with('creator')
            ->where('is_system', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByCreator(string $userId): Collection
    {
        return $this->model->with('creator')
            ->where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
