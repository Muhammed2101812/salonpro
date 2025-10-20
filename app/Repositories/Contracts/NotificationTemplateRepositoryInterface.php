<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\NotificationTemplate;

interface NotificationTemplateRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?NotificationTemplate;
    
    public function create(array $data): NotificationTemplate;
    
    public function update(int $id, array $data): NotificationTemplate;
    
    public function delete(int $id): bool;
    
    public function getByType(string $type): Collection;
    
    public function getByChannel(string $channel): Collection;
    
    public function getActive(): Collection;
    
    public function getByBranch(int $branchId): Collection;
}
