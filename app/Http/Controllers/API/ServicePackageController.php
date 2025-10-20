<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServicePackageRequest;
use App\Http\Requests\UpdateServicePackageRequest;
use App\Http\Resources\ServicePackageResource;
use App\Services\ServicePackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServicePackageController extends BaseController
{
    public function __construct(
        protected ServicePackageService $servicePackageService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $servicePackages = $this->servicePackageService->getPaginated($perPage);

            return $this->sendPaginated(
                ServicePackageResource::collection($servicePackages),
                'ServicePackages başarıyla getirildi'
            );
        }

        $servicePackages = $this->servicePackageService->getAll();

        return ServicePackageResource::collection($servicePackages);
    }

    public function store(StoreServicePackageRequest $request): JsonResponse
    {
        $servicePackage = $this->servicePackageService->create($request->validated());

        return $this->sendSuccess(
            new ServicePackageResource($servicePackage),
            'ServicePackage başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $servicePackage = $this->servicePackageService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServicePackageResource($servicePackage),
            'ServicePackage başarıyla getirildi'
        );
    }

    public function update(UpdateServicePackageRequest $request, string $id): JsonResponse
    {
        $servicePackage = $this->servicePackageService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServicePackageResource($servicePackage),
            'ServicePackage başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->servicePackageService->delete($id);

        return $this->sendSuccess(
            null,
            'ServicePackage başarıyla silindi'
        );
    }
}
