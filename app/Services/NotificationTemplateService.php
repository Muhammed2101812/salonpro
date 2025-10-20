<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;

class NotificationTemplateService extends BaseService
{
    public function __construct(NotificationTemplateRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
