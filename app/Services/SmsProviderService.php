<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\SmsProviderRepositoryInterface;

class SmsProviderService extends BaseService
{
    public function __construct(SmsProviderRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
