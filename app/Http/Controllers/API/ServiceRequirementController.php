<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServiceRequirementRequest;
use App\Http\Requests\UpdateServiceRequirementRequest;
use App\Http\Resources\ServiceRequirementResource;
use App\Services\ServiceRequirementService;
use App\Models\ServiceRequirement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceRequirementController extends BaseController
{
    public function __construct(
        protected ServiceRequirementService $serviceRequirementService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', ServiceRequirement::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $serviceRequirements = $this->serviceRequirementService->getPaginated($perPage);

            return $this->sendPaginated(
                ServiceRequirementResource::collection($serviceRequirements),
                'ServiceRequirements başarıyla getirildi'
            );
        }

        $serviceRequirements = $this->serviceRequirementService->getAll();

        return ServiceRequirementResource::collection($serviceRequirements);
    }

    public function store(StoreServiceRequirementRequest $request): JsonResponse
    {
        $this->authorize('create', ServiceRequirement::class);

        $serviceRequirement = $this->serviceRequirementService->create($request->validated());

        return $this->sendSuccess(
            new ServiceRequirementResource($serviceRequirement),
            'ServiceRequirement başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $serviceRequirement = $this->serviceRequirementService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServiceRequirementResource($serviceRequirement),
            'ServiceRequirement başarıyla getirildi'
        );
    }

    public function update(UpdateServiceRequirementRequest $request, string $id): JsonResponse
    {
        $serviceRequirement = $this->serviceRequirementService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServiceRequirementResource($serviceRequirement),
            'ServiceRequirement başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->serviceRequirementService->delete($id);

        return $this->sendSuccess(
            null,
            'ServiceRequirement başarıyla silindi'
        );
    }
}
