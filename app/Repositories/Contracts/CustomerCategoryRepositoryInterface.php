<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\CustomerCategory;
use Illuminate\Support\Collection;

interface CustomerCategoryRepositoryInterface
{
    public function all(string $branchId): Collection;
    public function find(string $id): ?CustomerCategory;
    public function create(array $data): CustomerCategory;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getActive(string $branchId): Collection;
    public function attachCustomers(string $categoryId, array $customerIds): void;
    public function detachCustomers(string $categoryId, array $customerIds): void;
    public function getCustomerCount(string $categoryId): int;
}
