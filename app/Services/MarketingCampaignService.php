<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\MarketingCampaignRepositoryInterface;

class MarketingCampaignService extends BaseService
{
    public function __construct(MarketingCampaignRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
