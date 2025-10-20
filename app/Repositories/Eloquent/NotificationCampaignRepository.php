<?php

namespace App\Repositories\Eloquent;

use App\Models\NotificationCampaign;
use App\Repositories\Contracts\NotificationCampaignRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationCampaignRepository implements NotificationCampaignRepositoryInterface
{
    public function __construct(protected NotificationCampaign $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->with(['branch', 'template', 'statistic'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['branch', 'template', 'statistic'])->paginate($perPage);
    }

    public function find(int $id): ?NotificationCampaign
    {
        return $this->model->with(['branch', 'template', 'statistic'])->find($id);
    }

    public function create(array $data): NotificationCampaign
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): NotificationCampaign
    {
        $campaign = $this->find($id);
        $campaign->update($data);
        return $campaign->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getScheduled(): Collection
    {
        return $this->model->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();
    }

    public function getActive(): Collection
    {
        return $this->model->where('status', 'active')->get();
    }

    public function getByChannel(string $channel): Collection
    {
        return $this->model->where('channel', $channel)->get();
    }

    public function updateStatistics(int $id, array $data): NotificationCampaign
    {
        $campaign = $this->find($id);
        $campaign->update($data);
        return $campaign->fresh();
    }
}
