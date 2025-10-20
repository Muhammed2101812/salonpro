<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\WebhookRepositoryInterface;

class WebhookService extends BaseService
{
    public function __construct(WebhookRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
