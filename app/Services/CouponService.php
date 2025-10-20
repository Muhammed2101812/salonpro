<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CouponRepositoryInterface;

class CouponService extends BaseService
{
    public function __construct(CouponRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
