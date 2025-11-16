<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface StockTransferServiceInterface
{
    public function createTransfer(array $data);
    public function updateTransfer(string $id, array $data);
    public function approveTransfer(string $id, string $userId);
    public function rejectTransfer(string $id, string $reason);
    public function completeTransfer(string $id);
    public function cancelTransfer(string $id, string $reason);
    public function getSourceBranchTransfers(string $branchId, int $perPage = 15);
    public function getDestinationBranchTransfers(string $branchId, int $perPage = 15);
    public function getPendingTransfers(?string $branchId = null, int $perPage = 15);
    public function getInTransitTransfers(?string $branchId = null, int $perPage = 15);
}
