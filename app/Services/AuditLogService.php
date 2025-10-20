<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AuditLogRepositoryInterface;

class AuditLogService extends BaseService
{
    public function __construct(AuditLogRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
