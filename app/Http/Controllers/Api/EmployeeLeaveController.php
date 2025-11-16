<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\EmployeeLeave\StoreEmployeeLeaveRequest;
use App\Http\Requests\EmployeeLeave\UpdateEmployeeLeaveRequest;
use App\Http\Resources\EmployeeLeaveResource;
use App\Services\Contracts\EmployeeLeaveServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeLeaveController extends BaseController
{
    public function __construct(
        protected EmployeeLeaveServiceInterface $leaveService
    ) {}

    /**
     * Display a listing of leaves.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $employeeId = $request->get('employee_id');
        $perPage = (int) $request->get('per_page', 15);

        if ($employeeId) {
            $leaves = $this->leaveService->getByEmployee($employeeId, $perPage);
        } else {
            $leaves = $this->leaveService->getPaginated($perPage);
        }

        return EmployeeLeaveResource::collection($leaves);
    }

    /**
     * Get pending leaves.
     */
    public function pending(Request $request): AnonymousResourceCollection
    {
        $employeeId = $request->get('employee_id');
        $leaves = $this->leaveService->getPending($employeeId);

        return EmployeeLeaveResource::collection($leaves);
    }

    /**
     * Request a new leave.
     */
    public function store(StoreEmployeeLeaveRequest $request): EmployeeLeaveResource
    {
        $leave = $this->leaveService->requestLeave($request->validated());

        return EmployeeLeaveResource::make($leave);
    }

    /**
     * Display the specified leave.
     */
    public function show(string $id): EmployeeLeaveResource
    {
        $leave = $this->leaveService->findByIdOrFail($id);

        return EmployeeLeaveResource::make($leave);
    }

    /**
     * Update the specified leave.
     */
    public function update(UpdateEmployeeLeaveRequest $request, string $id): EmployeeLeaveResource
    {
        $leave = $this->leaveService->update($id, $request->validated());

        return EmployeeLeaveResource::make($leave);
    }

    /**
     * Remove the specified leave.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->leaveService->delete($id);

        return response()->json(['message' => 'Leave deleted successfully']);
    }

    /**
     * Approve leave.
     */
    public function approve(Request $request, string $id): EmployeeLeaveResource
    {
        $leave = $this->leaveService->approve($id, auth()->id());

        return EmployeeLeaveResource::make($leave);
    }

    /**
     * Reject leave.
     */
    public function reject(Request $request, string $id): EmployeeLeaveResource
    {
        $request->validate(['reason' => 'nullable|string']);

        $leave = $this->leaveService->reject(
            $id,
            auth()->id(),
            $request->get('reason')
        );

        return EmployeeLeaveResource::make($leave);
    }

    /**
     * Cancel leave.
     */
    public function cancel(Request $request, string $id): EmployeeLeaveResource
    {
        $request->validate(['reason' => 'nullable|string']);

        $leave = $this->leaveService->cancel($id, $request->get('reason'));

        return EmployeeLeaveResource::make($leave);
    }

    /**
     * Get leave summary.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|uuid|exists:employees,id',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $summary = $this->leaveService->getSummary(
            $request->get('employee_id'),
            $request->get('year')
        );

        return response()->json(['data' => $summary]);
    }

    /**
     * Check for overlapping leaves.
     */
    public function checkOverlapping(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|uuid|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $hasOverlapping = $this->leaveService->checkOverlapping(
            $request->get('employee_id'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        return response()->json(['has_overlapping' => $hasOverlapping]);
    }
}
