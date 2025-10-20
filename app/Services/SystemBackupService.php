<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\SystemBackupRepositoryInterface;

class SystemBackupService extends BaseService
{
    public function __construct(SystemBackupRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
