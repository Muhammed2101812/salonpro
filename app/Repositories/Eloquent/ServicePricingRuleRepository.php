<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ServicePricingRule;
use App\Repositories\Contracts\ServicePricingRuleRepositoryInterface;

class ServicePricingRuleRepository extends BaseRepository implements ServicePricingRuleRepositoryInterface
{
    public function __construct(ServicePricingRule $model)
    {
        parent::__construct($model);
    }
}
