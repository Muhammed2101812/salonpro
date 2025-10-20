<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ReferralProgramRepositoryInterface;

class ReferralProgramService extends BaseService
{
    public function __construct(ReferralProgramRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
