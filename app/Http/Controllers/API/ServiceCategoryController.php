<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Http\Resources\ServiceCategoryResource;
use App\Services\ServiceCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceCategoryController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ServiceCategoryService $serviceCategoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $categories = $this->serviceCategoryService->getPaginated($perPage);

            return $this->sendPaginated(
                ServiceCategoryResource::collection($categories),
                'Service categories retrieved successfully'
            );
        }

        $categories = $this->serviceCategoryService->getAll();

        return ServiceCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceCategoryRequest $request): JsonResponse
    {
        $category = $this->serviceCategoryService->create($request->validated());

        return $this->sendSuccess(
            new ServiceCategoryResource($category),
            'Service category created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = $this->serviceCategoryService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServiceCategoryResource($category),
            'Service category retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceCategoryRequest $request, string $id): JsonResponse
    {
        $category = $this->serviceCategoryService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServiceCategoryResource($category),
            'Service category updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $this->serviceCategoryService->delete($id);

        return $this->sendSuccess(
            null,
            'Service category deleted successfully'
        );
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(string $id): JsonResponse
    {
        $this->serviceCategoryService->restore($id);

        return $this->sendSuccess(
            null,
            'Service category restored successfully'
        );
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDestroy(string $id): JsonResponse
    {
        $this->serviceCategoryService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Service category permanently deleted'
        );
    }
}
