<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\CustomerNote;
use App\Repositories\Contracts\CustomerNoteRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerNoteRepository implements CustomerNoteRepositoryInterface
{
    public function getByCustomer(string $customerId): Collection
    {
        return CustomerNote::where('customer_id', $customerId)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find(string $id): ?CustomerNote
    {
        return CustomerNote::find($id);
    }

    public function create(array $data): CustomerNote
    {
        return CustomerNote::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return CustomerNote::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return CustomerNote::where('id', $id)->delete();
    }

    public function getImportant(string $customerId): Collection
    {
        return CustomerNote::where('customer_id', $customerId)
            ->where('is_important', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPinned(string $customerId): Collection
    {
        return CustomerNote::where('customer_id', $customerId)
            ->where('is_pinned', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByType(string $customerId, string $type): Collection
    {
        return CustomerNote::where('customer_id', $customerId)
            ->where('note_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
