<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Services\Contracts\SupplierServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends BaseController
{
    public function __construct(
        protected SupplierServiceInterface $supplierService
    ) {}

    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->has('search')) {
            $suppliers = $this->supplierService->search(
                $request->get('search'),
                (int) $request->get('per_page', 15)
            );

            return SupplierResource::collection($suppliers);
        }

        if ($request->has('active')) {
            $suppliers = $this->supplierService->getActive();

            return SupplierResource::collection($suppliers);
        }

        $suppliers = $this->supplierService->getPaginated(
            (int) $request->get('per_page', 15)
        );

        return SupplierResource::collection($suppliers);
    }

    /**
     * Store a newly created supplier.
     */
    public function store(StoreSupplierRequest $request): SupplierResource
    {
        $supplier = $this->supplierService->create($request->validated());

        return SupplierResource::make($supplier);
    }

    /**
     * Display the specified supplier.
     */
    public function show(string $id): SupplierResource
    {
        $supplier = $this->supplierService->findByIdOrFail($id);

        return SupplierResource::make($supplier);
    }

    /**
     * Update the specified supplier.
     */
    public function update(UpdateSupplierRequest $request, string $id): SupplierResource
    {
        $supplier = $this->supplierService->update($id, $request->validated());

        return SupplierResource::make($supplier);
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->supplierService->delete($id);

        return response()->json(['message' => 'Supplier deleted successfully']);
    }

    /**
     * Get supplier statistics.
     */
    public function stats(string $id): JsonResponse
    {
        $stats = $this->supplierService->getSupplierStats($id);

        return response()->json(['data' => $stats]);
    }

    /**
     * Activate supplier.
     */
    public function activate(string $id): SupplierResource
    {
        $supplier = $this->supplierService->activate($id);

        return SupplierResource::make($supplier);
    }

    /**
     * Deactivate supplier.
     */
    public function deactivate(string $id): SupplierResource
    {
        $supplier = $this->supplierService->deactivate($id);

        return SupplierResource::make($supplier);
    }
}
