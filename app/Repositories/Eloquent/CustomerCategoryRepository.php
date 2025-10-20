<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\CustomerCategory;
use App\Repositories\Contracts\CustomerCategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerCategoryRepository implements CustomerCategoryRepositoryInterface
{
    public function all(string $branchId): Collection
    {
        return CustomerCategory::where('branch_id', $branchId)
            ->orderBy('sort_order')
            ->get();
    }

    public function find(string $id): ?CustomerCategory
    {
        return CustomerCategory::find($id);
    }

    public function create(array $data): CustomerCategory
    {
        return CustomerCategory::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return CustomerCategory::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return CustomerCategory::where('id', $id)->delete();
    }

    public function getActive(string $branchId): Collection
    {
        return CustomerCategory::where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function attachCustomers(string $categoryId, array $customerIds): void
    {
        $category = CustomerCategory::find($categoryId);
        $category->customers()->attach($customerIds);
    }

    public function detachCustomers(string $categoryId, array $customerIds): void
    {
        $category = CustomerCategory::find($categoryId);
        $category->customers()->detach($customerIds);
    }

    public function getCustomerCount(string $categoryId): int
    {
        $category = CustomerCategory::find($categoryId);
        return $category->customers()->count();
    }
}
