<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\WebhookLog;
use App\Repositories\Contracts\WebhookLogRepositoryInterface;
use Illuminate\Support\Collection;

class WebhookLogRepository extends BaseRepository implements WebhookLogRepositoryInterface
{
    public function __construct(WebhookLog $model)
    {
        parent::__construct($model);
    }

    public function getByWebhook(string $webhookId, int $perPage = 15): mixed
    {
        return $this->model->with('webhook')
            ->where('webhook_id', $webhookId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->with('webhook')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFailed(): Collection
    {
        return $this->model->with('webhook')
            ->where('status', 'failed')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingRetries(): Collection
    {
        return $this->model->with('webhook')
            ->where('status', 'pending')
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now())
            ->orderBy('next_retry_at', 'asc')
            ->get();
    }

    public function getByEvent(string $event): Collection
    {
        return $this->model->with('webhook')
            ->where('event', $event)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecent(int $limit = 50): Collection
    {
        return $this->model->with('webhook')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
