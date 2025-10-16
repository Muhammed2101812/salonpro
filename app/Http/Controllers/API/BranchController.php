<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Services\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BranchController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected BranchService $branchService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $branches = $this->branchService->getPaginated($perPage);

            return $this->sendPaginated(
                BranchResource::collection($branches),
                'Branches retrieved successfully'
            );
        }

        $branches = $this->branchService->getAll();

        return BranchResource::collection($branches);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBranchRequest $request): JsonResponse
    {
        $branch = $this->branchService->create($request->validated());

        return $this->sendSuccess(
            new BranchResource($branch),
            'Branch created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $branch = $this->branchService->findByIdOrFail($id);

        return $this->sendSuccess(
            new BranchResource($branch),
            'Branch retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBranchRequest $request, string $id): JsonResponse
    {
        $branch = $this->branchService->update($id, $request->validated());

        return $this->sendSuccess(
            new BranchResource($branch),
            'Branch updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $this->branchService->delete($id);

        return $this->sendSuccess(
            null,
            'Branch deleted successfully'
        );
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(string $id): JsonResponse
    {
        $this->branchService->restore($id);

        return $this->sendSuccess(
            null,
            'Branch restored successfully'
        );
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDestroy(string $id): JsonResponse
    {
        $this->branchService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Branch permanently deleted'
        );
    }
}
