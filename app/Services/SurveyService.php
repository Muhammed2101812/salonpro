<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\SurveyRepositoryInterface;

class SurveyService extends BaseService
{
    public function __construct(SurveyRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
