<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends BaseController
{
    public function __construct(
        protected SupplierService $supplierService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $suppliers = $this->supplierService->getPaginated($perPage);

            return $this->sendPaginated(
                SupplierResource::collection($suppliers),
                'Suppliers başarıyla getirildi'
            );
        }

        $suppliers = $this->supplierService->getAll();

        return SupplierResource::collection($suppliers);
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->create($request->validated());

        return $this->sendSuccess(
            new SupplierResource($supplier),
            'Supplier başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $supplier = $this->supplierService->findByIdOrFail($id);

        return $this->sendSuccess(
            new SupplierResource($supplier),
            'Supplier başarıyla getirildi'
        );
    }

    public function update(UpdateSupplierRequest $request, string $id): JsonResponse
    {
        $supplier = $this->supplierService->update($id, $request->validated());

        return $this->sendSuccess(
            new SupplierResource($supplier),
            'Supplier başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->supplierService->delete($id);

        return $this->sendSuccess(
            null,
            'Supplier başarıyla silindi'
        );
    }
}
