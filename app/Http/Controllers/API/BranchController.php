<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Services\BranchService;
use App\Models\Branch;
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
        $this->authorize('viewAny', Branch::class);

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
        $this->authorize('create', Branch::class);

        $branch = $this->branchService->create($request->validated());

        $this->authorize('view', $branch);


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

        $this->authorize('update', $branch);


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
        $branch = $this->branchService->findByIdOrFail($id);

        $this->authorize('delete', $branch);

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
        $branch = $this->branchService->findByIdOrFail($id);

        $this->authorize('restore', $branch);

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
        $branch = $this->branchService->findByIdOrFail($id);

        $this->authorize('forceDelete', $branch);

        $this->branchService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Branch permanently deleted'
        );
    }
}
