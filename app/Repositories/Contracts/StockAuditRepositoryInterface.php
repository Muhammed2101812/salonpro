<?php

namespace App\Repositories\Contracts;

use App\Models\StockAudit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StockAuditRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function findById(int $id): ?StockAudit;
    
    public function create(array $data): StockAudit;
    
    public function update(int $id, array $data): StockAudit;
    
    public function delete(int $id): bool;
    
    public function findByBranch(int $branchId): Collection;
    
    public function findByStatus(string $status): Collection;
    
    public function findByDateRange(string $startDate, string $endDate): Collection;
    
    public function findPending(): Collection;
    
    public function complete(int $id, array $data): StockAudit;
    
    public function cancel(int $id, string $reason): StockAudit;
}
