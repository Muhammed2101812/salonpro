<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeeLeaveRequest;
use App\Http\Requests\UpdateEmployeeLeaveRequest;
use App\Http\Resources\EmployeeLeaveResource;
use App\Services\EmployeeLeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeLeaveController extends BaseController
{
    public function __construct(
        protected EmployeeLeaveService $employeeLeaveService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employeeLeaves = $this->employeeLeaveService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeLeaveResource::collection($employeeLeaves),
                'EmployeeLeaves başarıyla getirildi'
            );
        }

        $employeeLeaves = $this->employeeLeaveService->getAll();

        return EmployeeLeaveResource::collection($employeeLeaves);
    }

    public function store(StoreEmployeeLeaveRequest $request): JsonResponse
    {
        $employeeLeave = $this->employeeLeaveService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeLeaveResource($employeeLeave),
            'EmployeeLeave başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employeeLeave = $this->employeeLeaveService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeeLeaveResource($employeeLeave),
            'EmployeeLeave başarıyla getirildi'
        );
    }

    public function update(UpdateEmployeeLeaveRequest $request, string $id): JsonResponse
    {
        $employeeLeave = $this->employeeLeaveService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeLeaveResource($employeeLeave),
            'EmployeeLeave başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeeLeaveService->delete($id);

        return $this->sendSuccess(
            null,
            'EmployeeLeave başarıyla silindi'
        );
    }
}
