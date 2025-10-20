<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CampaignStatisticRepositoryInterface;

class CampaignStatisticService extends BaseService
{
    public function __construct(CampaignStatisticRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
