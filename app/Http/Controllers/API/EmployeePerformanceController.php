<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeePerformanceRequest;
use App\Http\Requests\UpdateEmployeePerformanceRequest;
use App\Http\Resources\EmployeePerformanceResource;
use App\Services\EmployeePerformanceService;
use App\Models\EmployeePerformance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeePerformanceController extends BaseController
{
    public function __construct(
        protected EmployeePerformanceService $employeePerformanceService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', EmployeePerformance::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employeePerformances = $this->employeePerformanceService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeePerformanceResource::collection($employeePerformances),
                'EmployeePerformances başarıyla getirildi'
            );
        }

        $employeePerformances = $this->employeePerformanceService->getAll();

        return EmployeePerformanceResource::collection($employeePerformances);
    }

    public function store(StoreEmployeePerformanceRequest $request): JsonResponse
    {
        $this->authorize('create', EmployeePerformance::class);

        $employeePerformance = $this->employeePerformanceService->create($request->validated());

        return $this->sendSuccess(
            new EmployeePerformanceResource($employeePerformance),
            'EmployeePerformance başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employeePerformance = $this->employeePerformanceService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeePerformanceResource($employeePerformance),
            'EmployeePerformance başarıyla getirildi'
        );
    }

    public function update(UpdateEmployeePerformanceRequest $request, string $id): JsonResponse
    {
        $employeePerformance = $this->employeePerformanceService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeePerformanceResource($employeePerformance),
            'EmployeePerformance başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeePerformanceService->delete($id);

        return $this->sendSuccess(
            null,
            'EmployeePerformance başarıyla silindi'
        );
    }
}
