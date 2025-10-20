<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CustomerTag;
use App\Repositories\Contracts\CustomerTagRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerTagService
{
    public function __construct(
        private CustomerTagRepositoryInterface $repository
    ) {}

    public function getAllTags(string $branchId): Collection
    {
        return $this->repository->all($branchId);
    }

    public function getActiveTags(string $branchId): Collection
    {
        return $this->repository->getActive($branchId);
    }

    public function createTag(array $data): CustomerTag
    {
        return $this->repository->create($data);
    }

    public function updateTag(string $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteTag(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getMostPopularTags(string $branchId, int $limit = 10): Collection
    {
        return $this->repository->getMostUsed($branchId, $limit);
    }

    public function incrementUsage(string $tagId): void
    {
        $this->repository->incrementUsageCount($tagId);
    }

    public function decrementUsage(string $tagId): void
    {
        $this->repository->decrementUsageCount($tagId);
    }
}
