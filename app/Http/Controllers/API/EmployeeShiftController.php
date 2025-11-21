<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeeShiftRequest;
use App\Http\Requests\UpdateEmployeeShiftRequest;
use App\Http\Resources\EmployeeShiftResource;
use App\Services\EmployeeShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeShiftController extends BaseController
{
    public function __construct(
        protected EmployeeShiftService $employeeShiftService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        // Check if date range filter is provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $shifts = $this->employeeShiftService->getShiftsInRange(
                $request->get('start_date'),
                $request->get('end_date'),
                $request->get('branch_id')
            );

            return $this->sendSuccess(
                EmployeeShiftResource::collection($shifts),
                'Employee shifts retrieved successfully'
            );
        }

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $shifts = $this->employeeShiftService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeShiftResource::collection($shifts),
                'Employee shifts retrieved successfully'
            );
        }

        $shifts = $this->employeeShiftService->getAll();

        return EmployeeShiftResource::collection($shifts);
    }

    public function store(StoreEmployeeShiftRequest $request): JsonResponse
    {
        $shift = $this->employeeShiftService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeShiftResource($shift),
            'Employee shift created successfully',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $shift = $this->employeeShiftService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeeShiftResource($shift),
            'Employee shift retrieved successfully'
        );
    }

    public function update(UpdateEmployeeShiftRequest $request, string $id): JsonResponse
    {
        $shift = $this->employeeShiftService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeShiftResource($shift),
            'Employee shift updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeeShiftService->delete($id);

        return $this->sendSuccess(
            null,
            'Employee shift deleted successfully'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $this->employeeShiftService->restore($id);

        return $this->sendSuccess(
            null,
            'Employee shift restored successfully'
        );
    }

    public function forceDestroy(string $id): JsonResponse
    {
        $this->employeeShiftService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Employee shift permanently deleted'
        );
    }
}
