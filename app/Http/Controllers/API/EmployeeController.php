<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends BaseController
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Employee::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employees = $this->employeeService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeResource::collection($employees),
                'Employees retrieved successfully'
            );
        }

        $employees = $this->employeeService->getAll();

        return $this->sendSuccess(
            EmployeeResource::collection($employees),
            'Employees retrieved successfully'
        );
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $this->authorize('create', Employee::class);

        $employee = $this->employeeService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeResource($employee),
            'Employee created successfully',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employee = $this->employeeService->findByIdOrFail($id);

        $this->authorize('view', $employee);

        return $this->sendSuccess(
            new EmployeeResource($employee),
            'Employee retrieved successfully'
        );
    }

    public function update(UpdateEmployeeRequest $request, string $id): JsonResponse
    {
        $employee = $this->employeeService->findByIdOrFail($id);

        $this->authorize('update', $employee);

        $employee = $this->employeeService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeResource($employee),
            'Employee updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $employee = $this->employeeService->findByIdOrFail($id);

        $this->authorize('delete', $employee);

        $this->employeeService->delete($id);

        return $this->sendSuccess(
            null,
            'Employee deleted successfully'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $employee = $this->employeeService->findByIdOrFail($id);

        $this->authorize('restore', $employee);

        $this->employeeService->restore($id);

        return $this->sendSuccess(
            null,
            'Employee restored successfully'
        );
    }

    public function forceDestroy(string $id): JsonResponse
    {
        $employee = $this->employeeService->findByIdOrFail($id);

        $this->authorize('forceDelete', $employee);

        $this->employeeService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Employee permanently deleted'
        );
    }
}
