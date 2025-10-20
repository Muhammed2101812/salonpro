<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\CampaignStatistic;

interface CampaignStatisticRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?CampaignStatistic;
    
    public function create(array $data): CampaignStatistic;
    
    public function update(int $id, array $data): CampaignStatistic;
    
    public function delete(int $id): bool;
    
    public function getByCampaign(int $campaignId): ?CampaignStatistic;
    
    public function updateStatistics(int $campaignId, array $data): CampaignStatistic;
    
    public function calculateRates(int $campaignId): CampaignStatistic;
}
