<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\PurchaseOrderItemRepositoryInterface;

class PurchaseOrderItemService extends BaseService
{
    public function __construct(PurchaseOrderItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
