<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServiceTemplateRequest;
use App\Http\Requests\UpdateServiceTemplateRequest;
use App\Http\Resources\ServiceTemplateResource;
use App\Services\ServiceTemplateService;
use App\Models\ServiceTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceTemplateController extends BaseController
{
    public function __construct(
        protected ServiceTemplateService $serviceTemplateService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', ServiceTemplate::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $serviceTemplates = $this->serviceTemplateService->getPaginated($perPage);

            return $this->sendPaginated(
                ServiceTemplateResource::collection($serviceTemplates),
                'ServiceTemplates başarıyla getirildi'
            );
        }

        $serviceTemplates = $this->serviceTemplateService->getAll();

        return ServiceTemplateResource::collection($serviceTemplates);
    }

    public function store(StoreServiceTemplateRequest $request): JsonResponse
    {
        $this->authorize('create', ServiceTemplate::class);

        $serviceTemplate = $this->serviceTemplateService->create($request->validated());

        return $this->sendSuccess(
            new ServiceTemplateResource($serviceTemplate),
            'ServiceTemplate başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $serviceTemplate = $this->serviceTemplateService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServiceTemplateResource($serviceTemplate),
            'ServiceTemplate başarıyla getirildi'
        );
    }

    public function update(UpdateServiceTemplateRequest $request, string $id): JsonResponse
    {
        $serviceTemplate = $this->serviceTemplateService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServiceTemplateResource($serviceTemplate),
            'ServiceTemplate başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->serviceTemplateService->delete($id);

        return $this->sendSuccess(
            null,
            'ServiceTemplate başarıyla silindi'
        );
    }
}
