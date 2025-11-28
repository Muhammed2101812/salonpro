<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeeScheduleRequest;
use App\Http\Requests\UpdateEmployeeScheduleRequest;
use App\Http\Resources\EmployeeScheduleResource;
use App\Services\EmployeeScheduleService;
use App\Models\EmployeeSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeScheduleController extends BaseController
{
    public function __construct(
        protected EmployeeScheduleService $employeeScheduleService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', EmployeeSchedule::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employeeSchedules = $this->employeeScheduleService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeScheduleResource::collection($employeeSchedules),
                'EmployeeSchedules başarıyla getirildi'
            );
        }

        $employeeSchedules = $this->employeeScheduleService->getAll();

        return EmployeeScheduleResource::collection($employeeSchedules);
    }

    public function store(StoreEmployeeScheduleRequest $request): JsonResponse
    {
        $this->authorize('create', EmployeeSchedule::class);

        $employeeSchedule = $this->employeeScheduleService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeScheduleResource($employeeSchedule),
            'EmployeeSchedule başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employeeSchedule = $this->employeeScheduleService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeeScheduleResource($employeeSchedule),
            'EmployeeSchedule başarıyla getirildi'
        );
    }

    public function update(UpdateEmployeeScheduleRequest $request, string $id): JsonResponse
    {
        $employeeSchedule = $this->employeeScheduleService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeScheduleResource($employeeSchedule),
            'EmployeeSchedule başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeeScheduleService->delete($id);

        return $this->sendSuccess(
            null,
            'EmployeeSchedule başarıyla silindi'
        );
    }
}
