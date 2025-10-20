<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\InvoiceItemRepositoryInterface;

class InvoiceItemService extends BaseService
{
    public function __construct(InvoiceItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
