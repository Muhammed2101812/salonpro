<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\StockAuditItemRepositoryInterface;

class StockAuditItemService extends BaseService
{
    public function __construct(StockAuditItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
