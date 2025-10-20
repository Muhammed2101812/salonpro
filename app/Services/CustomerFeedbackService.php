<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CustomerFeedbackRepositoryInterface;

class CustomerFeedbackService extends BaseService
{
    public function __construct(CustomerFeedbackRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
