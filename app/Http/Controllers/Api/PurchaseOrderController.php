<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\UpdatePurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Services\Contracts\PurchaseOrderServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseOrderController extends BaseController
{
    public function __construct(
        protected PurchaseOrderServiceInterface $purchaseOrderService
    ) {}

    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->get('branch_id');
        $supplierId = $request->get('supplier_id');
        $perPage = (int) $request->get('per_page', 15);

        if ($supplierId) {
            $orders = $this->purchaseOrderService->getBySupplier($supplierId, $perPage);
        } elseif ($branchId) {
            $orders = $this->purchaseOrderService->getByBranch($branchId, $perPage);
        } else {
            $orders = $this->purchaseOrderService->getPaginated($perPage);
        }

        return PurchaseOrderResource::collection($orders);
    }

    /**
     * Get pending purchase orders.
     */
    public function pending(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->get('branch_id');
        $orders = $this->purchaseOrderService->getPending($branchId);

        return PurchaseOrderResource::collection($orders);
    }

    /**
     * Get overdue purchase orders.
     */
    public function overdue(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->get('branch_id');
        $orders = $this->purchaseOrderService->getOverdue($branchId);

        return PurchaseOrderResource::collection($orders);
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(StorePurchaseOrderRequest $request): PurchaseOrderResource
    {
        $purchaseOrder = $this->purchaseOrderService->createWithItems($request->validated());

        return PurchaseOrderResource::make($purchaseOrder);
    }

    /**
     * Display the specified purchase order.
     */
    public function show(string $id): PurchaseOrderResource
    {
        $purchaseOrder = $this->purchaseOrderService->findByIdOrFail($id);

        return PurchaseOrderResource::make($purchaseOrder);
    }

    /**
     * Update the specified purchase order.
     */
    public function update(UpdatePurchaseOrderRequest $request, string $id): PurchaseOrderResource
    {
        $purchaseOrder = $this->purchaseOrderService->update($id, $request->validated());

        return PurchaseOrderResource::make($purchaseOrder);
    }

    /**
     * Remove the specified purchase order.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->purchaseOrderService->delete($id);

        return response()->json(['message' => 'Purchase order deleted successfully']);
    }

    /**
     * Receive purchase order.
     */
    public function receive(Request $request, string $id): PurchaseOrderResource
    {
        $purchaseOrder = $this->purchaseOrderService->receive($id, $request->all());

        return PurchaseOrderResource::make($purchaseOrder);
    }

    /**
     * Cancel purchase order.
     */
    public function cancel(Request $request, string $id): PurchaseOrderResource
    {
        $purchaseOrder = $this->purchaseOrderService->cancel(
            $id,
            $request->get('reason')
        );

        return PurchaseOrderResource::make($purchaseOrder);
    }

    /**
     * Get purchase order totals by period.
     */
    public function totals(Request $request): JsonResponse
    {
        $totals = $this->purchaseOrderService->getTotalsByPeriod(
            $request->get('start_date'),
            $request->get('end_date'),
            $request->get('branch_id')
        );

        return response()->json(['data' => $totals]);
    }
}
