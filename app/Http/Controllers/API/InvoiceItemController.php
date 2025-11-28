<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreInvoiceItemRequest;
use App\Http\Requests\UpdateInvoiceItemRequest;
use App\Http\Resources\InvoiceItemResource;
use App\Services\InvoiceItemService;
use App\Models\InvoiceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceItemController extends BaseController
{
    public function __construct(
        protected InvoiceItemService $invoiceItemService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', InvoiceItem::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $invoiceItems = $this->invoiceItemService->getPaginated($perPage);

            return $this->sendPaginated(
                InvoiceItemResource::collection($invoiceItems),
                'InvoiceItems başarıyla getirildi'
            );
        }

        $invoiceItems = $this->invoiceItemService->getAll();

        return InvoiceItemResource::collection($invoiceItems);
    }

    public function store(StoreInvoiceItemRequest $request): JsonResponse
    {
        $this->authorize('create', InvoiceItem::class);

        $invoiceItem = $this->invoiceItemService->create($request->validated());

        return $this->sendSuccess(
            new InvoiceItemResource($invoiceItem),
            'InvoiceItem başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $invoiceItem = $this->invoiceItemService->findByIdOrFail($id);

        return $this->sendSuccess(
            new InvoiceItemResource($invoiceItem),
            'InvoiceItem başarıyla getirildi'
        );
    }

    public function update(UpdateInvoiceItemRequest $request, string $id): JsonResponse
    {
        $invoiceItem = $this->invoiceItemService->update($id, $request->validated());

        return $this->sendSuccess(
            new InvoiceItemResource($invoiceItem),
            'InvoiceItem başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->invoiceItemService->delete($id);

        return $this->sendSuccess(
            null,
            'InvoiceItem başarıyla silindi'
        );
    }
}
