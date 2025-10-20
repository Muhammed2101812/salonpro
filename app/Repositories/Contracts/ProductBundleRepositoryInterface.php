<?php

namespace App\Repositories\Contracts;

use App\Models\ProductBundle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductBundleRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function findById(int $id): ?ProductBundle;
    
    public function create(array $data): ProductBundle;
    
    public function update(int $id, array $data): ProductBundle;
    
    public function delete(int $id): bool;
    
    public function findByBranch(int $branchId): Collection;
    
    public function findActive(): Collection;
    
    public function findByDateRange(string $startDate, string $endDate): Collection;
    
    public function activate(int $id): ProductBundle;
    
    public function deactivate(int $id): ProductBundle;
    
    public function checkAvailability(int $id): bool;
}
