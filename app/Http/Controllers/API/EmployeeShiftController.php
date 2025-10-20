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
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employeeShifts = $this->employeeShiftService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeShiftResource::collection($employeeShifts),
                'EmployeeShifts başarıyla getirildi'
            );
        }

        $employeeShifts = $this->employeeShiftService->getAll();

        return EmployeeShiftResource::collection($employeeShifts);
    }

    public function store(StoreEmployeeShiftRequest $request): JsonResponse
    {
        $employeeShift = $this->employeeShiftService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeShiftResource($employeeShift),
            'EmployeeShift başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employeeShift = $this->employeeShiftService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeeShiftResource($employeeShift),
            'EmployeeShift başarıyla getirildi'
        );
    }

    public function update(UpdateEmployeeShiftRequest $request, string $id): JsonResponse
    {
        $employeeShift = $this->employeeShiftService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeShiftResource($employeeShift),
            'EmployeeShift başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeeShiftService->delete($id);

        return $this->sendSuccess(
            null,
            'EmployeeShift başarıyla silindi'
        );
    }
}
