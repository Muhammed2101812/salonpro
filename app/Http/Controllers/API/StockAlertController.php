<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAlert\StoreStockAlertRequest;
use App\Http\Requests\StockAlert\UpdateStockAlertRequest;
use App\Http\Resources\StockAlertResource;
use App\Services\Contracts\StockAlertServiceInterface;
use App\Models\StockAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockAlertController extends Controller
{
    public function __construct(
        protected StockAlertServiceInterface $stockAlertService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', StockAlert::class);

        $branchId = $request->query('branch_id');
        $perPage = (int) $request->query('per_page', 15);

        $alerts = $branchId
            ? $this->stockAlertService->getByBranch($branchId, $perPage)
            : $this->stockAlertService->getAll($perPage);

        return StockAlertResource::collection($alerts);
    }

    public function store(StoreStockAlertRequest $request): JsonResponse
    {
        $this->authorize('create', StockAlert::class);

        $alert = $this->stockAlertService->create($request->validated());

        return response()->json([
            'message' => 'Stock alert created successfully',
            'data' => StockAlertResource::make($alert),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $alert = $this->stockAlertService->findById($id);

        return response()->json([
            'data' => StockAlertResource::make($alert),
        ]);
    }

    public function update(UpdateStockAlertRequest $request, string $id): JsonResponse
    {
        $alert = $this->stockAlertService->update($id, $request->validated());

        return response()->json([
            'message' => 'Stock alert updated successfully',
            'data' => StockAlertResource::make($alert),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->stockAlertService->delete($id);

        return response()->json([
            'message' => 'Stock alert deleted successfully',
        ]);
    }

    public function active(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->query('branch_id');
        $alerts = $this->stockAlertService->getActive($branchId);

        return StockAlertResource::collection($alerts);
    }

    public function resolved(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->query('branch_id');
        $alerts = $this->stockAlertService->getResolved($branchId);

        return StockAlertResource::collection($alerts);
    }

    public function critical(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->query('branch_id');
        $alerts = $this->stockAlertService->getCritical($branchId);

        return StockAlertResource::collection($alerts);
    }

    public function markAsNotified(string $id): JsonResponse
    {
        $alert = $this->stockAlertService->markAsNotified($id);

        return response()->json([
            'message' => 'Stock alert marked as notified',
            'data' => StockAlertResource::make($alert),
        ]);
    }

    public function resolve(Request $request, string $id): JsonResponse
    {
        $notes = $request->input('notes');
        $alert = $this->stockAlertService->resolve($id, $notes);

        return response()->json([
            'message' => 'Stock alert resolved successfully',
            'data' => StockAlertResource::make($alert),
        ]);
    }
}
