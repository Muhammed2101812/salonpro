<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface StockTransferRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySourceBranch(string $branchId, int $perPage = 15);
    public function findByDestinationBranch(string $branchId, int $perPage = 15);
    public function findByStatus(string $status, int $perPage = 15);
    public function getPendingTransfers(?string $branchId = null, int $perPage = 15);
    public function getInTransitTransfers(?string $branchId = null, int $perPage = 15);
    public function getTransfersByDateRange(string $startDate, string $endDate, ?string $branchId = null);
}
