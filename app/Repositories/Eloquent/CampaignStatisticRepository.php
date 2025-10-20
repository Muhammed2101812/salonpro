<?php

namespace App\Repositories\Eloquent;

use App\Models\CampaignStatistic;
use App\Repositories\Contracts\CampaignStatisticRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CampaignStatisticRepository implements CampaignStatisticRepositoryInterface
{
    public function __construct(protected CampaignStatistic $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->with(['branch', 'campaign'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['branch', 'campaign'])->paginate($perPage);
    }

    public function find(int $id): ?CampaignStatistic
    {
        return $this->model->with(['branch', 'campaign'])->find($id);
    }

    public function create(array $data): CampaignStatistic
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): CampaignStatistic
    {
        $statistic = $this->find($id);
        $statistic->update($data);
        return $statistic->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getByCampaign(int $campaignId): ?CampaignStatistic
    {
        return $this->model->where('campaign_id', $campaignId)->first();
    }

    public function updateStatistics(int $campaignId, array $data): CampaignStatistic
    {
        $statistic = $this->getByCampaign($campaignId);
        
        if (!$statistic) {
            $data['campaign_id'] = $campaignId;
            return $this->create($data);
        }
        
        $statistic->update($data);
        return $statistic->fresh();
    }

    public function calculateRates(int $campaignId): CampaignStatistic
    {
        $statistic = $this->getByCampaign($campaignId);
        
        if (!$statistic || $statistic->total_sent == 0) {
            return $statistic;
        }
        
        $deliveryRate = ($statistic->total_delivered / $statistic->total_sent) * 100;
        $readRate = ($statistic->total_read / $statistic->total_sent) * 100;
        $clickRate = ($statistic->total_clicked / $statistic->total_sent) * 100;
        $unsubscribeRate = ($statistic->total_unsubscribed / $statistic->total_sent) * 100;
        
        $statistic->update([
            'delivery_rate' => round($deliveryRate, 2),
            'read_rate' => round($readRate, 2),
            'click_rate' => round($clickRate, 2),
            'unsubscribe_rate' => round($unsubscribeRate, 2),
        ]);
        
        return $statistic->fresh();
    }
}
