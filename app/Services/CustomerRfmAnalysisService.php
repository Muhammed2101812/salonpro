<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CustomerRfmAnalysisRepositoryInterface;

class CustomerRfmAnalysisService extends BaseService
{
    public function __construct(CustomerRfmAnalysisRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
