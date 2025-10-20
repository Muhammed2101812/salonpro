<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeCommission;
use App\Repositories\Contracts\EmployeeCommissionRepositoryInterface;
use Illuminate\Support\Collection;

class EmployeeCommissionRepository implements EmployeeCommissionRepositoryInterface
{
    public function all(): Collection
    {
        return EmployeeCommission::with(['employee', 'appointment', 'sale'])->get();
    }

    public function find(string $id): ?EmployeeCommission
    {
        return EmployeeCommission::with(['employee', 'appointment', 'sale'])->find($id);
    }

    public function create(array $data): EmployeeCommission
    {
        return EmployeeCommission::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return EmployeeCommission::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return EmployeeCommission::where('id', $id)->delete();
    }

    public function getByEmployee(string $employeeId): Collection
    {
        return EmployeeCommission::with(['appointment', 'sale'])
            ->where('employee_id', $employeeId)
            ->orderBy('commission_date', 'desc')
            ->get();
    }

    public function getUnpaid(string $employeeId = null): Collection
    {
        $query = EmployeeCommission::with(['employee', 'appointment', 'sale'])
            ->where('is_paid', false);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->orderBy('commission_date')
            ->get();
    }

    public function getPaid(string $employeeId = null): Collection
    {
        $query = EmployeeCommission::with(['employee', 'appointment', 'sale'])
            ->where('is_paid', true);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->orderBy('paid_date', 'desc')
            ->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return EmployeeCommission::with(['employee', 'appointment', 'sale'])
            ->whereBetween('commission_date', [$startDate, $endDate])
            ->orderBy('commission_date', 'desc')
            ->get();
    }

    public function getTotalCommissionForEmployee(string $employeeId, string $startDate = null, string $endDate = null): float
    {
        $query = EmployeeCommission::where('employee_id', $employeeId);

        if ($startDate && $endDate) {
            $query->whereBetween('commission_date', [$startDate, $endDate]);
        }

        return (float) $query->sum('commission_amount');
    }

    public function markAsPaid(array $commissionIds, string $paidDate): bool
    {
        return EmployeeCommission::whereIn('id', $commissionIds)
            ->update([
                'is_paid' => true,
                'paid_date' => $paidDate,
            ]);
    }
}
