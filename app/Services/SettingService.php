<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingService extends BaseService
{
    public function __construct(SettingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
