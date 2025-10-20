<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeeCommissionRequest;
use App\Http\Requests\UpdateEmployeeCommissionRequest;
use App\Http\Resources\EmployeeCommissionResource;
use App\Services\EmployeeCommissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeCommissionController extends BaseController
{
    public function __construct(
        protected EmployeeCommissionService $employeeCommissionService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employeeCommissions = $this->employeeCommissionService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeCommissionResource::collection($employeeCommissions),
                'EmployeeCommissions başarıyla getirildi'
            );
        }

        $employeeCommissions = $this->employeeCommissionService->getAll();

        return EmployeeCommissionResource::collection($employeeCommissions);
    }

    public function store(StoreEmployeeCommissionRequest $request): JsonResponse
    {
        $employeeCommission = $this->employeeCommissionService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeCommissionResource($employeeCommission),
            'EmployeeCommission başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employeeCommission = $this->employeeCommissionService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeeCommissionResource($employeeCommission),
            'EmployeeCommission başarıyla getirildi'
        );
    }

    public function update(UpdateEmployeeCommissionRequest $request, string $id): JsonResponse
    {
        $employeeCommission = $this->employeeCommissionService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeCommissionResource($employeeCommission),
            'EmployeeCommission başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeeCommissionService->delete($id);

        return $this->sendSuccess(
            null,
            'EmployeeCommission başarıyla silindi'
        );
    }
}
