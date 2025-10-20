<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeeLeave;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class EmployeeLeaveService
{
    /**
     * Get all leave requests
     */
    public function getAllLeaves(): Collection
    {
        return EmployeeLeave::with(['employee', 'approver'])
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Get leave requests for a specific employee
     */
    public function getEmployeeLeaves(string $employeeId, ?string $status = null): Collection
    {
        $query = EmployeeLeave::where('employee_id', $employeeId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    /**
     * Get pending leave requests
     */
    public function getPendingLeaves(): Collection
    {
        return EmployeeLeave::with('employee')
            ->where('status', 'pending')
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get approved leaves
     */
    public function getApprovedLeaves(?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = EmployeeLeave::with('employee')
            ->where('status', 'approved');

        if ($startDate) {
            $query->where(function ($q) use ($startDate) {
                $q->whereDate('end_date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->where(function ($q) use ($endDate) {
                $q->whereDate('start_date', '<=', $endDate);
            });
        }

        return $query->orderBy('start_date')->get();
    }

    /**
     * Get leaves by type
     */
    public function getLeavesByType(string $leaveType, ?string $status = null): Collection
    {
        $query = EmployeeLeave::with('employee')
            ->where('leave_type', $leaveType);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    /**
     * Create a new leave request
     */
    public function createLeave(array $data): EmployeeLeave
    {
        // Validate dates
        $this->validateDates($data['start_date'], $data['end_date']);

        // Validate leave type
        $this->validateLeaveType($data['leave_type']);

        // Calculate total days if not provided
        if (!isset($data['total_days'])) {
            $data['total_days'] = $this->calculateTotalDays($data['start_date'], $data['end_date']);
        }

        // Check for overlapping leaves
        $this->checkOverlappingLeaves($data['employee_id'], $data['start_date'], $data['end_date']);

        // Set default status
        $data['status'] = $data['status'] ?? 'pending';

        return EmployeeLeave::create($data);
    }

    /**
     * Update leave request
     */
    public function updateLeave(string $id, array $data): bool
    {
        $leave = EmployeeLeave::find($id);

        if (!$leave) {
            throw new \Exception('İzin kaydı bulunamadı.');
        }

        // Prevent updating approved/rejected leaves
        if ($leave->status !== 'pending' && !isset($data['status'])) {
            throw new \Exception('Onaylanmış veya reddedilmiş izin kayıtları güncellenemez.');
        }

        // Validate dates if both are provided
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $this->validateDates($data['start_date'], $data['end_date']);
            $data['total_days'] = $this->calculateTotalDays($data['start_date'], $data['end_date']);

            // Check for overlapping leaves (excluding current leave)
            $this->checkOverlappingLeaves(
                $leave->employee_id,
                $data['start_date'],
                $data['end_date'],
                $id
            );
        }

        // Validate leave type if provided
        if (isset($data['leave_type'])) {
            $this->validateLeaveType($data['leave_type']);
        }

        return $leave->update($data);
    }

    /**
     * Delete leave request
     */
    public function deleteLeave(string $id): bool
    {
        $leave = EmployeeLeave::find($id);

        if (!$leave) {
            throw new \Exception('İzin kaydı bulunamadı.');
        }

        // Only pending leaves can be deleted
        if ($leave->status !== 'pending') {
            throw new \Exception('Sadece beklemedeki izin talepleri silinebilir.');
        }

        return $leave->delete();
    }

    /**
     * Approve leave request
     */
    public function approveLeave(string $id, string $approvedBy): bool
    {
        $leave = EmployeeLeave::find($id);

        if (!$leave) {
            throw new \Exception('İzin kaydı bulunamadı.');
        }

        if (!$leave->isPending()) {
            throw new \Exception('Sadece beklemedeki izin talepleri onaylanabilir.');
        }

        return $leave->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject leave request
     */
    public function rejectLeave(string $id, string $approvedBy, string $rejectionReason): bool
    {
        $leave = EmployeeLeave::find($id);

        if (!$leave) {
            throw new \Exception('İzin kaydı bulunamadı.');
        }

        if (!$leave->isPending()) {
            throw new \Exception('Sadece beklemedeki izin talepleri reddedilebilir.');
        }

        return $leave->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'rejection_reason' => $rejectionReason,
        ]);
    }

    /**
     * Cancel approved leave
     */
    public function cancelLeave(string $id): bool
    {
        $leave = EmployeeLeave::find($id);

        if (!$leave) {
            throw new \Exception('İzin kaydı bulunamadı.');
        }

        if (!$leave->isApproved()) {
            throw new \Exception('Sadece onaylanmış izinler iptal edilebilir.');
        }

        // Check if leave has started
        if ($leave->start_date->isPast()) {
            throw new \Exception('Başlamış olan izinler iptal edilemez.');
        }

        return $leave->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Get employee leave balance
     */
    public function getEmployeeLeaveBalance(string $employeeId, ?string $leaveType = null, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        $query = EmployeeLeave::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereYear('start_date', $year);

        if ($leaveType) {
            $query->where('leave_type', $leaveType);
        }

        $leaves = $query->get();

        // Default annual leave days (can be configured per employee)
        $annualLeaveDays = 14; // Turkish law default

        $usedDays = $leaves->sum('total_days');

        return [
            'year' => $year,
            'total_allocated' => $annualLeaveDays,
            'used_days' => $usedDays,
            'remaining_days' => max(0, $annualLeaveDays - $usedDays),
            'leave_type' => $leaveType,
        ];
    }

    /**
     * Get leave statistics for employee
     */
    public function getEmployeeLeaveStats(string $employeeId, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        $leaves = EmployeeLeave::where('employee_id', $employeeId)
            ->whereYear('start_date', $year)
            ->get();

        $byStatus = $leaves->groupBy('status');
        $byType = $leaves->where('status', 'approved')->groupBy('leave_type');

        return [
            'year' => $year,
            'total_requests' => $leaves->count(),
            'approved' => $byStatus->get('approved', collect())->count(),
            'pending' => $byStatus->get('pending', collect())->count(),
            'rejected' => $byStatus->get('rejected', collect())->count(),
            'cancelled' => $byStatus->get('cancelled', collect())->count(),
            'total_days_taken' => $byStatus->get('approved', collect())->sum('total_days'),
            'by_type' => $byType->map(fn($leaves) => [
                'count' => $leaves->count(),
                'total_days' => $leaves->sum('total_days'),
            ])->toArray(),
        ];
    }

    /**
     * Get upcoming leaves
     */
    public function getUpcomingLeaves(int $days = 30): Collection
    {
        return EmployeeLeave::with('employee')
            ->where('status', 'approved')
            ->whereDate('start_date', '>=', now())
            ->whereDate('start_date', '<=', now()->addDays($days))
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get current leaves (employees on leave today)
     */
    public function getCurrentLeaves(): Collection
    {
        return EmployeeLeave::with('employee')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();
    }

    /**
     * Check if employee is on leave
     */
    public function isEmployeeOnLeave(string $employeeId, ?Carbon $date = null): bool
    {
        $date = $date ?? now();

        return EmployeeLeave::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }

    /**
     * Validate dates
     */
    private function validateDates(string $startDate, string $endDate): void
    {
        $start = new Carbon($startDate);
        $end = new Carbon($endDate);

        if ($end->lt($start)) {
            throw new \InvalidArgumentException(
                'Bitiş tarihi başlangıç tarihinden önce olamaz.'
            );
        }

        // Check if start date is in the past
        if ($start->isPast() && !$start->isToday()) {
            throw new \InvalidArgumentException(
                'İzin başlangıç tarihi geçmiş bir tarih olamaz.'
            );
        }
    }

    /**
     * Validate leave type
     */
    private function validateLeaveType(string $leaveType): void
    {
        $validTypes = ['annual', 'sick', 'unpaid', 'maternity', 'paternity', 'emergency', 'other'];

        if (!in_array($leaveType, $validTypes)) {
            throw new \InvalidArgumentException(
                'Geçersiz izin türü. Geçerli türler: ' . implode(', ', $validTypes)
            );
        }
    }

    /**
     * Calculate total days
     */
    private function calculateTotalDays(string $startDate, string $endDate): int
    {
        $start = new Carbon($startDate);
        $end = new Carbon($endDate);

        // Add 1 to include both start and end dates
        return $start->diffInDays($end) + 1;
    }

    /**
     * Check for overlapping leaves
     */
    private function checkOverlappingLeaves(
        string $employeeId,
        string $startDate,
        string $endDate,
        ?string $excludeId = null
    ): void {
        $query = EmployeeLeave::where('employee_id', $employeeId)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new \Exception(
                'Belirlenen tarihler için çakışan bir izin kaydı var.'
            );
        }
    }
}
