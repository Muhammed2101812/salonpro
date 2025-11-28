<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServiceAddonRequest;
use App\Http\Requests\UpdateServiceAddonRequest;
use App\Http\Resources\ServiceAddonResource;
use App\Services\ServiceAddonService;
use App\Models\ServiceAddon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceAddonController extends BaseController
{
    public function __construct(
        protected ServiceAddonService $serviceAddonService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', ServiceAddon::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $serviceAddons = $this->serviceAddonService->getPaginated($perPage);

            return $this->sendPaginated(
                ServiceAddonResource::collection($serviceAddons),
                'ServiceAddons başarıyla getirildi'
            );
        }

        $serviceAddons = $this->serviceAddonService->getAll();

        return ServiceAddonResource::collection($serviceAddons);
    }

    public function store(StoreServiceAddonRequest $request): JsonResponse
    {
        $this->authorize('create', ServiceAddon::class);

        $serviceAddon = $this->serviceAddonService->create($request->validated());

        return $this->sendSuccess(
            new ServiceAddonResource($serviceAddon),
            'ServiceAddon başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $serviceAddon = $this->serviceAddonService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServiceAddonResource($serviceAddon),
            'ServiceAddon başarıyla getirildi'
        );
    }

    public function update(UpdateServiceAddonRequest $request, string $id): JsonResponse
    {
        $serviceAddon = $this->serviceAddonService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServiceAddonResource($serviceAddon),
            'ServiceAddon başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->serviceAddonService->delete($id);

        return $this->sendSuccess(
            null,
            'ServiceAddon başarıyla silindi'
        );
    }
}
