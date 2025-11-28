<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockTransferResource;
use App\Services\Contracts\StockTransferServiceInterface;
use App\Models\StockTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockTransferController extends Controller
{
    public function __construct(
        private StockTransferServiceInterface $stockTransferService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\StockTransfer::class);

        if ($request->has('source_branch_id')) {
            $transfers = $this->stockTransferService->getSourceBranchTransfers(
                $request->input('source_branch_id'),
                $request->input('per_page', 15)
            );
        } elseif ($request->has('destination_branch_id')) {
            $transfers = $this->stockTransferService->getDestinationBranchTransfers(
                $request->input('destination_branch_id'),
                $request->input('per_page', 15)
            );
        } elseif ($request->input('status') === 'pending') {
            $transfers = $this->stockTransferService->getPendingTransfers(
                $request->input('branch_id'),
                $request->input('per_page', 15)
            );
        } elseif ($request->input('status') === 'in_transit') {
            $transfers = $this->stockTransferService->getInTransitTransfers(
                $request->input('branch_id'),
                $request->input('per_page', 15)
            );
        } else {
            $transfers = \App\Models\StockTransfer::with([
                'fromBranch', 'toBranch', 'productVariant.product', 'createdBy', 'approvedBy'
            ])->paginate($request->input('per_page', 15));
        }

        return StockTransferResource::collection($transfers);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\StockTransfer::class);

        $validated = $request->validate([
            'from_branch_id' => 'required|uuid|exists:branches,id|different:to_branch_id',
            'to_branch_id' => 'required|uuid|exists:branches,id',
            'product_variant_id' => 'required|uuid|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'transfer_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $transfer = $this->stockTransferService->createTransfer($validated);

        return StockTransferResource::make($transfer)->response()->setStatusCode(201);
    }

    public function show(string $id): StockTransferResource
    {
        $transfer = \App\Models\StockTransfer::with([
            'fromBranch', 'toBranch', 'productVariant.product', 'createdBy', 'approvedBy', 'receivedBy'
        ])->findOrFail($id);

        $this->authorize('view', $transfer);

        return StockTransferResource::make($transfer);
    }

    public function update(Request $request, string $id): StockTransferResource
    {
        $transfer = \App\Models\StockTransfer::findOrFail($id);
        $this->authorize('update', $transfer);

        $validated = $request->validate([
            'from_branch_id' => 'sometimes|uuid|exists:branches,id|different:to_branch_id',
            'to_branch_id' => 'sometimes|uuid|exists:branches,id',
            'product_variant_id' => 'sometimes|uuid|exists:product_variants,id',
            'quantity' => 'sometimes|integer|min:1',
            'transfer_date' => 'sometimes|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $updated = $this->stockTransferService->updateTransfer($id, $validated);

        return StockTransferResource::make($updated);
    }

    public function destroy(string $id): JsonResponse
    {
        $transfer = \App\Models\StockTransfer::findOrFail($id);
        $this->authorize('delete', $transfer);

        $transfer->delete();

        return response()->json(['message' => 'Stock transfer deleted successfully']);
    }

    public function approve(string $id): StockTransferResource
    {
        $transfer = \App\Models\StockTransfer::findOrFail($id);
        $this->authorize('approve', $transfer);

        $approved = $this->stockTransferService->approveTransfer($id, auth()->id());

        return StockTransferResource::make($approved);
    }

    public function reject(Request $request, string $id): StockTransferResource
    {
        $transfer = \App\Models\StockTransfer::findOrFail($id);
        $this->authorize('approve', $transfer);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $rejected = $this->stockTransferService->rejectTransfer($id, $validated['reason']);

        return StockTransferResource::make($rejected);
    }

    public function complete(string $id): StockTransferResource
    {
        $transfer = \App\Models\StockTransfer::findOrFail($id);
        $this->authorize('complete', $transfer);

        $completed = $this->stockTransferService->completeTransfer($id);

        return StockTransferResource::make($completed);
    }

    public function cancel(Request $request, string $id): StockTransferResource
    {
        $transfer = \App\Models\StockTransfer::findOrFail($id);
        $this->authorize('cancel', $transfer);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $cancelled = $this->stockTransferService->cancelTransfer($id, $validated['reason']);

        return StockTransferResource::make($cancelled);
    }
}
