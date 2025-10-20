<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreIntegrationRequest;
use App\Http\Requests\UpdateIntegrationRequest;
use App\Http\Resources\IntegrationResource;
use App\Services\IntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IntegrationController extends BaseController
{
    public function __construct(
        protected IntegrationService $integrationService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $integrations = $this->integrationService->getPaginated($perPage);

            return $this->sendPaginated(
                IntegrationResource::collection($integrations),
                'Integrations başarıyla getirildi'
            );
        }

        $integrations = $this->integrationService->getAll();

        return IntegrationResource::collection($integrations);
    }

    public function store(StoreIntegrationRequest $request): JsonResponse
    {
        $integration = $this->integrationService->create($request->validated());

        return $this->sendSuccess(
            new IntegrationResource($integration),
            'Integration başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $integration = $this->integrationService->findByIdOrFail($id);

        return $this->sendSuccess(
            new IntegrationResource($integration),
            'Integration başarıyla getirildi'
        );
    }

    public function update(UpdateIntegrationRequest $request, string $id): JsonResponse
    {
        $integration = $this->integrationService->update($id, $request->validated());

        return $this->sendSuccess(
            new IntegrationResource($integration),
            'Integration başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->integrationService->delete($id);

        return $this->sendSuccess(
            null,
            'Integration başarıyla silindi'
        );
    }
}
