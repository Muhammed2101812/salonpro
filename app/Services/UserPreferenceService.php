<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\UserPreferenceRepositoryInterface;

class UserPreferenceService extends BaseService
{
    public function __construct(UserPreferenceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
