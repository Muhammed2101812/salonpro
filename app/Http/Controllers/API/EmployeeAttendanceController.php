<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeeAttendanceRequest;
use App\Http\Requests\UpdateEmployeeAttendanceRequest;
use App\Http\Resources\EmployeeAttendanceResource;
use App\Services\EmployeeAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeAttendanceController extends BaseController
{
    public function __construct(
        protected EmployeeAttendanceService $employeeAttendanceService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employeeAttendances = $this->employeeAttendanceService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeAttendanceResource::collection($employeeAttendances),
                'EmployeeAttendances başarıyla getirildi'
            );
        }

        $employeeAttendances = $this->employeeAttendanceService->getAll();

        return EmployeeAttendanceResource::collection($employeeAttendances);
    }

    public function store(StoreEmployeeAttendanceRequest $request): JsonResponse
    {
        $employeeAttendance = $this->employeeAttendanceService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeAttendanceResource($employeeAttendance),
            'EmployeeAttendance başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employeeAttendance = $this->employeeAttendanceService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeeAttendanceResource($employeeAttendance),
            'EmployeeAttendance başarıyla getirildi'
        );
    }

    public function update(UpdateEmployeeAttendanceRequest $request, string $id): JsonResponse
    {
        $employeeAttendance = $this->employeeAttendanceService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeAttendanceResource($employeeAttendance),
            'EmployeeAttendance başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeeAttendanceService->delete($id);

        return $this->sendSuccess(
            null,
            'EmployeeAttendance başarıyla silindi'
        );
    }
}
