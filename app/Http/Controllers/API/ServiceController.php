<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ServiceService $serviceService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $services = $this->serviceService->getPaginated($perPage);

            return $this->sendPaginated(
                ServiceResource::collection($services),
                'Services retrieved successfully'
            );
        }

        $services = $this->serviceService->getAll();

        return ServiceResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->serviceService->create($request->validated());

        return $this->sendSuccess(
            new ServiceResource($service),
            'Service created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $service = $this->serviceService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServiceResource($service),
            'Service retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, string $id): JsonResponse
    {
        $service = $this->serviceService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServiceResource($service),
            'Service updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $this->serviceService->delete($id);

        return $this->sendSuccess(
            null,
            'Service deleted successfully'
        );
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(string $id): JsonResponse
    {
        $this->serviceService->restore($id);

        return $this->sendSuccess(
            null,
            'Service restored successfully'
        );
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDestroy(string $id): JsonResponse
    {
        $this->serviceService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Service permanently deleted'
        );
    }
}
