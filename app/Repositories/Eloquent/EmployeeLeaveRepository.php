<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeLeave;
use App\Repositories\Contracts\EmployeeLeaveRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeLeaveRepository implements EmployeeLeaveRepositoryInterface
{
    public function all(): Collection
    {
        return EmployeeLeave::with(['employee', 'approvedBy'])->get();
    }

    public function find(string $id): ?EmployeeLeave
    {
        return EmployeeLeave::with(['employee', 'approvedBy'])->find($id);
    }

    public function create(array $data): EmployeeLeave
    {
        return EmployeeLeave::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return EmployeeLeave::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return EmployeeLeave::where('id', $id)->delete();
    }

    public function getByEmployee(string $employeeId): Collection
    {
        return EmployeeLeave::with('approvedBy')
            ->where('employee_id', $employeeId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return EmployeeLeave::with(['employee', 'approvedBy'])
            ->where('status', $status)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getByLeaveType(string $leaveType): Collection
    {
        return EmployeeLeave::with(['employee', 'approvedBy'])
            ->where('leave_type', $leaveType)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getPending(): Collection
    {
        return EmployeeLeave::with('employee')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return EmployeeLeave::with(['employee', 'approvedBy'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->orderBy('start_date')
            ->get();
    }

    public function getTotalLeaveDays(string $employeeId, string $leaveType = null, int $year = null): int
    {
        $query = EmployeeLeave::where('employee_id', $employeeId)
            ->where('status', 'approved');

        if ($leaveType) {
            $query->where('leave_type', $leaveType);
        }

        if ($year) {
            $query->whereYear('start_date', $year);
        }

        return (int) $query->sum('total_days');
    }

    public function approve(string $id, string $approvedBy): bool
    {
        return EmployeeLeave::where('id', $id)->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => Carbon::now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(string $id, string $approvedBy, string $reason): bool
    {
        return EmployeeLeave::where('id', $id)->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'approved_at' => Carbon::now(),
            'rejection_reason' => $reason,
        ]);
    }
}
