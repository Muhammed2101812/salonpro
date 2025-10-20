<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\CustomerNote;
use Illuminate\Support\Collection;

interface CustomerNoteRepositoryInterface
{
    public function getByCustomer(string $customerId): Collection;
    public function find(string $id): ?CustomerNote;
    public function create(array $data): CustomerNote;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getImportant(string $customerId): Collection;
    public function getPinned(string $customerId): Collection;
    public function getByType(string $customerId, string $type): Collection;
}
