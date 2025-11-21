<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\EmployeeShiftRepositoryInterface;

class EmployeeShiftService extends BaseService
{
    public function __construct(EmployeeShiftRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getShiftsInRange(string $startDate, string $endDate, ?string $branchId = null): array
    {
        return $this->repository->getShiftsInRange($startDate, $endDate, $branchId);
    }
}
