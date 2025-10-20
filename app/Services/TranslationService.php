<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\TranslationRepositoryInterface;

class TranslationService extends BaseService
{
    public function __construct(TranslationRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
