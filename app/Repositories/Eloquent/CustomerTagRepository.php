<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\CustomerTag;
use App\Repositories\Contracts\CustomerTagRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerTagRepository implements CustomerTagRepositoryInterface
{
    public function all(string $branchId): Collection
    {
        return CustomerTag::where('branch_id', $branchId)
            ->orderBy('usage_count', 'desc')
            ->get();
    }

    public function find(string $id): ?CustomerTag
    {
        return CustomerTag::find($id);
    }

    public function create(array $data): CustomerTag
    {
        return CustomerTag::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return CustomerTag::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return CustomerTag::where('id', $id)->delete();
    }

    public function getActive(string $branchId): Collection
    {
        return CustomerTag::where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->get();
    }

    public function incrementUsageCount(string $tagId): void
    {
        CustomerTag::where('id', $tagId)->increment('usage_count');
    }

    public function decrementUsageCount(string $tagId): void
    {
        CustomerTag::where('id', $tagId)
            ->where('usage_count', '>', 0)
            ->decrement('usage_count');
    }

    public function getMostUsed(string $branchId, int $limit = 10): Collection
    {
        return CustomerTag::where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
