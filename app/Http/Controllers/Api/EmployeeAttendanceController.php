<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\EmployeeAttendance\StoreEmployeeAttendanceRequest;
use App\Http\Requests\EmployeeAttendance\UpdateEmployeeAttendanceRequest;
use App\Http\Resources\EmployeeAttendanceResource;
use App\Services\Contracts\EmployeeAttendanceServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeAttendanceController extends BaseController
{
    public function __construct(
        protected EmployeeAttendanceServiceInterface $attendanceService
    ) {}

    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $employeeId = $request->get('employee_id');
        $branchId = $request->get('branch_id');
        $perPage = (int) $request->get('per_page', 15);

        if ($employeeId) {
            $attendance = $this->attendanceService->getByEmployee($employeeId, $perPage);
        } else {
            $attendance = $this->attendanceService->getPaginated($perPage);
        }

        return EmployeeAttendanceResource::collection($attendance);
    }

    /**
     * Get today's attendance.
     */
    public function today(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->get('branch_id');
        $attendance = $this->attendanceService->getToday($branchId);

        return EmployeeAttendanceResource::collection($attendance);
    }

    /**
     * Get active (clocked in) employees.
     */
    public function active(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->get('branch_id');
        $attendance = $this->attendanceService->getActive($branchId);

        return EmployeeAttendanceResource::collection($attendance);
    }

    /**
     * Clock in employee.
     */
    public function clockIn(StoreEmployeeAttendanceRequest $request): EmployeeAttendanceResource
    {
        $attendance = $this->attendanceService->clockIn($request->validated());

        return EmployeeAttendanceResource::make($attendance);
    }

    /**
     * Clock out employee.
     */
    public function clockOut(Request $request, string $id): EmployeeAttendanceResource
    {
        $attendance = $this->attendanceService->clockOut($id, $request->all());

        return EmployeeAttendanceResource::make($attendance);
    }

    /**
     * Start break.
     */
    public function startBreak(string $id): EmployeeAttendanceResource
    {
        $attendance = $this->attendanceService->startBreak($id);

        return EmployeeAttendanceResource::make($attendance);
    }

    /**
     * End break.
     */
    public function endBreak(string $id): EmployeeAttendanceResource
    {
        $attendance = $this->attendanceService->endBreak($id);

        return EmployeeAttendanceResource::make($attendance);
    }

    /**
     * Get attendance summary.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|uuid|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $summary = $this->attendanceService->getSummary(
            $request->get('employee_id'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        return response()->json(['data' => $summary]);
    }

    /**
     * Display the specified attendance.
     */
    public function show(string $id): EmployeeAttendanceResource
    {
        $attendance = $this->attendanceService->findByIdOrFail($id);

        return EmployeeAttendanceResource::make($attendance);
    }

    /**
     * Update the specified attendance.
     */
    public function update(UpdateEmployeeAttendanceRequest $request, string $id): EmployeeAttendanceResource
    {
        $attendance = $this->attendanceService->update($id, $request->validated());

        return EmployeeAttendanceResource::make($attendance);
    }

    /**
     * Remove the specified attendance.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->attendanceService->delete($id);

        return response()->json(['message' => 'Attendance record deleted successfully']);
    }
}
