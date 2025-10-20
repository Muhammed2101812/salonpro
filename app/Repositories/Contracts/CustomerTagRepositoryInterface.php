<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\CustomerTag;
use Illuminate\Support\Collection;

interface CustomerTagRepositoryInterface
{
    public function all(string $branchId): Collection;
    public function find(string $id): ?CustomerTag;
    public function create(array $data): CustomerTag;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getActive(string $branchId): Collection;
    public function incrementUsageCount(string $tagId): void;
    public function decrementUsageCount(string $tagId): void;
    public function getMostUsed(string $branchId, int $limit = 10): Collection;
}
