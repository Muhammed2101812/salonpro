<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreInventoryMovementRequest;
use App\Http\Requests\UpdateInventoryMovementRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Services\InventoryMovementService;
use App\Models\InventoryMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InventoryMovementController extends BaseController
{
    public function __construct(
        protected InventoryMovementService $inventoryMovementService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', InventoryMovement::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $movements = $this->inventoryMovementService->getPaginated($perPage);

            return $this->sendPaginated(
                InventoryMovementResource::collection($movements),
                'Inventory movements retrieved successfully'
            );
        }

        $movements = $this->inventoryMovementService->getAll();

        return InventoryMovementResource::collection($movements);
    }

    public function store(StoreInventoryMovementRequest $request): JsonResponse
    {
        $this->authorize('create', InventoryMovement::class);

        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()?->id; // Set current user, null safe

            $movement = $this->inventoryMovementService->create($data);

            return $this->sendSuccess(
                new InventoryMovementResource($movement->load(['product', 'user'])),
                'Inventory movement created successfully',
                201
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->sendError('Product not found', [], 404);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create inventory movement: '.$e->getMessage(), [], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        $movement = $this->inventoryMovementService->findByIdOrFail($id);

        return $this->sendSuccess(
            new InventoryMovementResource($movement->load(['product', 'user'])),
            'Inventory movement retrieved successfully'
        );
    }

    public function update(UpdateInventoryMovementRequest $request, string $id): JsonResponse
    {
        $movement = $this->inventoryMovementService->update($id, $request->validated());

        return $this->sendSuccess(
            new InventoryMovementResource($movement->load(['product', 'user'])),
            'Inventory movement updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->inventoryMovementService->delete($id);

        return $this->sendSuccess(
            null,
            'Inventory movement deleted successfully'
        );
    }
}
