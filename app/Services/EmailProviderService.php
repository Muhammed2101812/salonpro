<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\EmailProviderRepositoryInterface;

class EmailProviderService extends BaseService
{
    public function __construct(EmailProviderRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
