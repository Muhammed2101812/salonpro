<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\NotificationQueueRepositoryInterface;

class NotificationQueueService extends BaseService
{
    public function __construct(NotificationQueueRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
