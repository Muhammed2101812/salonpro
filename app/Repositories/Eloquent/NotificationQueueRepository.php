<?php

namespace App\Repositories\Eloquent;

use App\Models\NotificationQueue;
use App\Repositories\Contracts\NotificationQueueRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationQueueRepository implements NotificationQueueRepositoryInterface
{
    public function __construct(protected NotificationQueue $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->with(['branch', 'template', 'campaign', 'recipient'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['branch', 'template', 'campaign', 'recipient'])->paginate($perPage);
    }

    public function find(int $id): ?NotificationQueue
    {
        return $this->model->with(['branch', 'template', 'campaign', 'recipient'])->find($id);
    }

    public function create(array $data): NotificationQueue
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): NotificationQueue
    {
        $queue = $this->find($id);
        $queue->update($data);
        return $queue->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getPending(): Collection
    {
        return $this->model->where('status', 'pending')
            ->whereNull('scheduled_at')
            ->orWhere('scheduled_at', '<=', now())
            ->get();
    }

    public function getScheduled(): Collection
    {
        return $this->model->where('status', 'pending')
            ->where('scheduled_at', '>', now())
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getByChannel(string $channel): Collection
    {
        return $this->model->where('channel', $channel)->get();
    }

    public function getByRecipient(string $type, int $id): Collection
    {
        return $this->model->where('recipient_type', $type)
            ->where('recipient_id', $id)
            ->get();
    }

    public function getByCampaign(int $campaignId): Collection
    {
        return $this->model->where('campaign_id', $campaignId)->get();
    }
}
