<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends BaseController
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $invoices = $this->invoiceService->getPaginated($perPage);

            return $this->sendPaginated(
                InvoiceResource::collection($invoices),
                'Invoices başarıyla getirildi'
            );
        }

        $invoices = $this->invoiceService->getAll();

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->create($request->validated());

        return $this->sendSuccess(
            new InvoiceResource($invoice),
            'Invoice başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $invoice = $this->invoiceService->findByIdOrFail($id);

        return $this->sendSuccess(
            new InvoiceResource($invoice),
            'Invoice başarıyla getirildi'
        );
    }

    public function update(UpdateInvoiceRequest $request, string $id): JsonResponse
    {
        $invoice = $this->invoiceService->update($id, $request->validated());

        return $this->sendSuccess(
            new InvoiceResource($invoice),
            'Invoice başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->invoiceService->delete($id);

        return $this->sendSuccess(
            null,
            'Invoice başarıyla silindi'
        );
    }
}
