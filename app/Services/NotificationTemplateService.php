<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;

class NotificationTemplateService extends BaseService
{
    public function __construct(NotificationTemplateRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function findBySlug(string $slug): ?object
    {
        return $this->repository->findBySlug($slug);
    }

    public function findByEventAndChannel(string $eventType, string $channel): ?object
    {
        return $this->repository->findByEventAndChannel($eventType, $channel);
    }
}
