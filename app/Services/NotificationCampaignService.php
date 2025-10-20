<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\NotificationCampaignRepositoryInterface;

class NotificationCampaignService extends BaseService
{
    public function __construct(NotificationCampaignRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
