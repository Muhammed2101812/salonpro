<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CustomFieldRepositoryInterface;

class CustomFieldService extends BaseService
{
    public function __construct(CustomFieldRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
