<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CustomFieldValueRepositoryInterface;

class CustomFieldValueService extends BaseService
{
    public function __construct(CustomFieldValueRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
