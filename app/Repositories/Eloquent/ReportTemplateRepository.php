<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ReportTemplate;
use App\Repositories\Contracts\ReportTemplateRepositoryInterface;

class ReportTemplateRepository extends BaseRepository implements ReportTemplateRepositoryInterface
{
    public function __construct(ReportTemplate $model)
    {
        parent::__construct($model);
    }

    public function getActiveTemplates()
    {
        return $this->model->where('is_active', true)
            ->orderBy('category')
            ->orderBy('template_name')
            ->get();
    }

    public function findByCategory(string $category)
    {
        return $this->model->where('category', $category)
            ->where('is_active', true)
            ->orderBy('template_name')
            ->get();
    }

    public function findByCode(string $code)
    {
        return $this->model->where('template_code', $code)->first();
    }
}
