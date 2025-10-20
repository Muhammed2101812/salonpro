<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CustomerNote;
use App\Repositories\Contracts\CustomerNoteRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerNoteService
{
    public function __construct(
        private CustomerNoteRepositoryInterface $repository
    ) {}

    public function getCustomerNotes(string $customerId): Collection
    {
        return $this->repository->getByCustomer($customerId);
    }

    public function createNote(array $data): CustomerNote
    {
        return $this->repository->create($data);
    }

    public function updateNote(string $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteNote(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getImportantNotes(string $customerId): Collection
    {
        return $this->repository->getImportant($customerId);
    }

    public function getPinnedNotes(string $customerId): Collection
    {
        return $this->repository->getPinned($customerId);
    }

    public function getNotesByType(string $customerId, string $type): Collection
    {
        return $this->repository->getByType($customerId, $type);
    }

    public function pinNote(string $id): bool
    {
        return $this->repository->update($id, ['is_pinned' => true]);
    }

    public function unpinNote(string $id): bool
    {
        return $this->repository->update($id, ['is_pinned' => false]);
    }

    public function markAsImportant(string $id): bool
    {
        return $this->repository->update($id, ['is_important' => true]);
    }

    public function markAsNotImportant(string $id): bool
    {
        return $this->repository->update($id, ['is_important' => false]);
    }
}
