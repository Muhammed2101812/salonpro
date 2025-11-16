<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\EmployeeLeaveRepositoryInterface;
use App\Services\Contracts\EmployeeLeaveServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeLeaveService extends BaseService implements EmployeeLeaveServiceInterface
{
    public function __construct(
        protected EmployeeLeaveRepositoryInterface $leaveRepository
    ) {
        parent::__construct($leaveRepository);
    }

    public function getByEmployee(string $employeeId, int $perPage = 15): mixed
    {
        return $this->leaveRepository->findByEmployee($employeeId, $perPage);
    }

    public function getPending(?string $employeeId = null): mixed
    {
        return $this->leaveRepository->findPending($employeeId);
    }

    public function requestLeave(array $data): mixed
    {
        return DB::transaction(function () use ($data) {
            // Check for overlapping leaves
            $overlapping = $this->leaveRepository->findOverlapping(
                $data['employee_id'],
                $data['start_date'],
                $data['end_date']
            );

            if ($overlapping->isNotEmpty()) {
                throw new \RuntimeException('Employee has overlapping leave request');
            }

            // Calculate total days
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $data['total_days'] = $startDate->diffInDays($endDate) + 1;

            // Set default status
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }

            return $this->leaveRepository->create($data);
        });
    }

    public function approve(string $id, string $approvedBy): mixed
    {
        return DB::transaction(function () use ($id, $approvedBy) {
            $leave = $this->leaveRepository->findOrFail($id);

            if ($leave->status !== 'pending') {
                throw new \RuntimeException('Only pending leaves can be approved');
            }

            return $this->leaveRepository->update($id, [
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);
        });
    }

    public function reject(string $id, string $approvedBy, ?string $reason = null): mixed
    {
        return DB::transaction(function () use ($id, $approvedBy, $reason) {
            $leave = $this->leaveRepository->findOrFail($id);

            if ($leave->status !== 'pending') {
                throw new \RuntimeException('Only pending leaves can be rejected');
            }

            $updateData = [
                'status' => 'rejected',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ];

            if ($reason) {
                $updateData['notes'] = ($leave->notes ?? '') . "\nRejection reason: {$reason}";
            }

            return $this->leaveRepository->update($id, $updateData);
        });
    }

    public function cancel(string $id, ?string $reason = null): mixed
    {
        return DB::transaction(function () use ($id, $reason) {
            $leave = $this->leaveRepository->findOrFail($id);

            if (!in_array($leave->status, ['pending', 'approved'])) {
                throw new \RuntimeException('Only pending or approved leaves can be cancelled');
            }

            $updateData = ['status' => 'cancelled'];

            if ($reason) {
                $updateData['notes'] = ($leave->notes ?? '') . "\nCancellation reason: {$reason}";
            }

            return $this->leaveRepository->update($id, $updateData);
        });
    }

    public function getSummary(string $employeeId, string $year): array
    {
        return $this->leaveRepository->getSummary($employeeId, $year);
    }

    public function checkOverlapping(string $employeeId, string $startDate, string $endDate): bool
    {
        $overlapping = $this->leaveRepository->findOverlapping($employeeId, $startDate, $endDate);

        return $overlapping->isNotEmpty();
    }
}
