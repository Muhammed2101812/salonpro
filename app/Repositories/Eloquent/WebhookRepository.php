<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Webhook;
use App\Repositories\Contracts\WebhookRepositoryInterface;
use Illuminate\Support\Collection;

class WebhookRepository extends BaseRepository implements WebhookRepositoryInterface
{
    public function __construct(Webhook $model)
    {
        parent::__construct($model);
    }

    public function getActive(?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'creator'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getByEvent(string $event, ?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'creator'])
            ->where('is_active', true)
            ->whereJsonContains('events', $event)
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getByBranch(string $branchId): Collection
    {
        return $this->model->with(['branch', 'creator'])
            ->where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function incrementSuccessCount(string $id): mixed
    {
        $webhook = $this->findOrFail($id);
        $webhook->increment('success_count');
        $webhook->refresh();
        return $webhook;
    }

    public function incrementFailureCount(string $id): mixed
    {
        $webhook = $this->findOrFail($id);
        $webhook->increment('failure_count');
        $webhook->refresh();
        return $webhook;
    }

    public function updateLastTriggered(string $id): mixed
    {
        return $this->update($id, [
            'last_triggered_at' => now(),
        ]);
    }
}
