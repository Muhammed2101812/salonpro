<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\NotificationTemplate;
use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;

class NotificationTemplateRepository extends BaseRepository implements NotificationTemplateRepositoryInterface
{
    public function __construct(NotificationTemplate $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?object
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function findByEventAndChannel(string $eventType, string $channel): ?object
    {
        return $this->model
            ->where('event_type', $eventType)
            ->where('channel', $channel)
            ->where('is_active', true)
            ->first();
    }
}
