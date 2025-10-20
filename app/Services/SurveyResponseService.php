<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\SurveyResponseRepositoryInterface;

class SurveyResponseService extends BaseService
{
    public function __construct(SurveyResponseRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
