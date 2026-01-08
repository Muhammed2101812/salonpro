<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\EmployeeCommission\StoreEmployeeCommissionRequest;
use App\Http\Requests\EmployeeCommission\UpdateEmployeeCommissionRequest;
use App\Http\Resources\EmployeeCommissionResource;
use App\Services\Contracts\EmployeeCommissionServiceInterface;
use App\Models\EmployeeCommission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeCommissionController extends BaseController
{
    public function __construct(
        protected EmployeeCommissionServiceInterface $commissionService
    ) {}

    /**
     * Display a listing of commissions.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', EmployeeCommission::class);

        $employeeId = $request->get('employee_id');
        $perPage = (int) $request->get('per_page', 15);

        if ($employeeId) {
            $commissions = $this->commissionService->getByEmployee($employeeId, $perPage);
        } else {
            $commissions = $this->commissionService->getPaginated($perPage);
        }

        return EmployeeCommissionResource::collection($commissions);
    }

    /**
     * Get unpaid commissions.
     */
    public function unpaid(Request $request): AnonymousResourceCollection
    {
        $employeeId = $request->get('employee_id');
        $commissions = $this->commissionService->getUnpaid($employeeId);

        return EmployeeCommissionResource::collection($commissions);
    }

    /**
     * Store a newly created commission.
     */
    public function store(StoreEmployeeCommissionRequest $request): EmployeeCommissionResource
    {
        $this->authorize('create', EmployeeCommission::class);

        $commission = $this->commissionService->create($request->validated());

        return EmployeeCommissionResource::make($commission);
    }

    /**
     * Display the specified commission.
     */
    public function show(string $id): EmployeeCommissionResource
    {
        $commission = $this->commissionService->findByIdOrFail($id);

        return EmployeeCommissionResource::make($commission);
    }

    /**
     * Update the specified commission.
     */
    public function update(UpdateEmployeeCommissionRequest $request, string $id): EmployeeCommissionResource
    {
        $commission = $this->commissionService->update($id, $request->validated());

        return EmployeeCommissionResource::make($commission);
    }

    /**
     * Remove the specified commission.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->commissionService->delete($id);

        return response()->json(['message' => 'Commission deleted successfully']);
    }

    /**
     * Mark commission as paid.
     */
    public function markAsPaid(string $id): EmployeeCommissionResource
    {
        $commission = $this->commissionService->markAsPaid($id);

        return EmployeeCommissionResource::make($commission);
    }

    /**
     * Mark multiple commissions as paid.
     */
    public function markMultipleAsPaid(Request $request): JsonResponse
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'required|uuid|exists:employee_commissions,id',
        ]);

        $results = $this->commissionService->markMultipleAsPaid($request->get('commission_ids'));

        return response()->json(['data' => $results]);
    }

    /**
     * Get commission summary.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|uuid|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $summary = $this->commissionService->getSummary(
            $request->get('employee_id'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        return response()->json(['data' => $summary]);
    }

    /**
     * Calculate commission.
     */
    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'base_amount' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $amount = $this->commissionService->calculateCommission(
            $request->get('base_amount'),
            $request->get('commission_rate')
        );

        return response()->json(['commission_amount' => $amount]);
    }
}
