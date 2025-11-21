<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface NotificationTemplateRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?object;
    public function findByEventAndChannel(string $eventType, string $channel): ?object;
}
