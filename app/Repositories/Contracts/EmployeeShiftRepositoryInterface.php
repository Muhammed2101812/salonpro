<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface EmployeeShiftRepositoryInterface extends BaseRepositoryInterface
{
    public function getShiftsInRange(string $startDate, string $endDate, ?string $branchId = null): array;
}
