<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\SystemSettingRepositoryInterface;

class SystemSettingService extends BaseService
{
    public function __construct(SystemSettingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
