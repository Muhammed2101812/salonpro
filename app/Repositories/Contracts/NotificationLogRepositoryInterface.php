<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\NotificationLog;

interface NotificationLogRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?NotificationLog;
    
    public function create(array $data): NotificationLog;
    
    public function update(int $id, array $data): NotificationLog;
    
    public function delete(int $id): bool;
    
    public function getByStatus(string $status): Collection;
    
    public function getByChannel(string $channel): Collection;
    
    public function getByRecipient(string $type, int $id): Collection;
    
    public function getByDateRange(string $start, string $end): Collection;
}
