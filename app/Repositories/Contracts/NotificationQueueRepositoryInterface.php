<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\NotificationQueue;

interface NotificationQueueRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?NotificationQueue;
    
    public function create(array $data): NotificationQueue;
    
    public function update(int $id, array $data): NotificationQueue;
    
    public function delete(int $id): bool;
    
    public function getPending(): Collection;
    
    public function getScheduled(): Collection;
    
    public function getByStatus(string $status): Collection;
    
    public function getByChannel(string $channel): Collection;
    
    public function getByRecipient(string $type, int $id): Collection;
    
    public function getByCampaign(int $campaignId): Collection;
}
