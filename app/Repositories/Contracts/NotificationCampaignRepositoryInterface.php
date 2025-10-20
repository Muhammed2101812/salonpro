<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\NotificationCampaign;

interface NotificationCampaignRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?NotificationCampaign;
    
    public function create(array $data): NotificationCampaign;
    
    public function update(int $id, array $data): NotificationCampaign;
    
    public function delete(int $id): bool;
    
    public function getByStatus(string $status): Collection;
    
    public function getScheduled(): Collection;
    
    public function getActive(): Collection;
    
    public function getByChannel(string $channel): Collection;
    
    public function updateStatistics(int $id, array $data): NotificationCampaign;
}
