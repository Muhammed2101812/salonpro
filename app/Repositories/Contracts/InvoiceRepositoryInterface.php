<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface InvoiceRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCustomer(string $customerId, int $perPage = 15);
    public function findByBranch(string $branchId, int $perPage = 15);
    public function findByStatus(string $status, int $perPage = 15);
    public function getTotalsByPeriod(string $startDate, string $endDate, ?string $branchId = null);
}
