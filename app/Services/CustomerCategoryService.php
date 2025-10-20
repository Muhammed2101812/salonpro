<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CustomerCategory;
use App\Repositories\Contracts\CustomerCategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerCategoryService
{
    public function __construct(
        private CustomerCategoryRepositoryInterface $repository
    ) {}

    public function getAllCategories(string $branchId): Collection
    {
        return $this->repository->all($branchId);
    }

    public function getActiveCategories(string $branchId): Collection
    {
        return $this->repository->getActive($branchId);
    }

    public function createCategory(array $data): CustomerCategory
    {
        return $this->repository->create($data);
    }

    public function updateCategory(string $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteCategory(string $id): bool
    {
        // Check if category has customers
        $customerCount = $this->repository->getCustomerCount($id);

        if ($customerCount > 0) {
            throw new \Exception('Bu kategoriye ait müşteriler var. Önce müşterileri başka kategoriye taşıyın.');
        }

        return $this->repository->delete($id);
    }

    public function assignCustomers(string $categoryId, array $customerIds): void
    {
        $this->repository->attachCustomers($categoryId, $customerIds);
    }

    public function removeCustomers(string $categoryId, array $customerIds): void
    {
        $this->repository->detachCustomers($categoryId, $customerIds);
    }
}
