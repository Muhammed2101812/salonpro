<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeeCertificationRequest;
use App\Http\Requests\UpdateEmployeeCertificationRequest;
use App\Http\Resources\EmployeeCertificationResource;
use App\Services\EmployeeCertificationService;
use App\Models\EmployeeCertification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeCertificationController extends BaseController
{
    public function __construct(
        protected EmployeeCertificationService $employeeCertificationService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', EmployeeCertification::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employeeCertifications = $this->employeeCertificationService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeCertificationResource::collection($employeeCertifications),
                'EmployeeCertifications başarıyla getirildi'
            );
        }

        $employeeCertifications = $this->employeeCertificationService->getAll();

        return EmployeeCertificationResource::collection($employeeCertifications);
    }

    public function store(StoreEmployeeCertificationRequest $request): JsonResponse
    {
        $this->authorize('create', EmployeeCertification::class);

        $employeeCertification = $this->employeeCertificationService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeCertificationResource($employeeCertification),
            'EmployeeCertification başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employeeCertification = $this->employeeCertificationService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeeCertificationResource($employeeCertification),
            'EmployeeCertification başarıyla getirildi'
        );
    }

    public function update(UpdateEmployeeCertificationRequest $request, string $id): JsonResponse
    {
        $employeeCertification = $this->employeeCertificationService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeCertificationResource($employeeCertification),
            'EmployeeCertification başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeeCertificationService->delete($id);

        return $this->sendSuccess(
            null,
            'EmployeeCertification başarıyla silindi'
        );
    }
}
