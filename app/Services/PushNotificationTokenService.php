<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\PushNotificationTokenRepositoryInterface;

class PushNotificationTokenService extends BaseService
{
    public function __construct(PushNotificationTokenRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
