<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\JournalEntryRepositoryInterface;

class JournalEntryService extends BaseService
{
    public function __construct(JournalEntryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
