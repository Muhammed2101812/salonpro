<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\NotificationPreferenceRepositoryInterface;

class NotificationPreferenceService extends BaseService
{
    public function __construct(NotificationPreferenceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
