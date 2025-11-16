<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeShift;
use App\Repositories\Contracts\EmployeeShiftRepositoryInterface;

class EmployeeShiftRepository extends BaseRepository implements EmployeeShiftRepositoryInterface
{
    public function __construct(EmployeeShift $model)
    {
        parent::__construct($model);
    }

    public function getShiftsInRange(string $startDate, string $endDate, ?string $branchId = null): array
    {
        $query = $this->model->newQuery()
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->with(['employee', 'branch'])
            ->orderBy('shift_date')
            ->orderBy('start_time');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get()->toArray();
    }
}
