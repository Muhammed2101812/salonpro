<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\JournalEntryLineRepositoryInterface;

class JournalEntryLineService extends BaseService
{
    public function __construct(JournalEntryLineRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
