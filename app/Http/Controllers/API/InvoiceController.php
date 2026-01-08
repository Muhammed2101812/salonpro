<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Services\Contracts\InvoiceServiceInterface;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceServiceInterface $invoiceService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\Invoice::class);

        if ($request->has('customer_id')) {
            $invoices = $this->invoiceService->getCustomerInvoices(
                $request->input('customer_id'),
                $request->input('per_page', 15)
            );
        } elseif ($request->has('branch_id')) {
            $invoices = $this->invoiceService->getBranchInvoices(
                $request->input('branch_id'),
                $request->input('per_page', 15)
            );
        } elseif ($request->has('status')) {
            $invoices = $this->invoiceService->getInvoicesByStatus(
                $request->input('status'),
                $request->input('per_page', 15)
            );
        } else {
            // Default to branch invoices for current user
            $invoices = $this->invoiceService->getBranchInvoices(
                auth()->user()->branch_id,
                $request->input('per_page', 15)
            );
        }

        return InvoiceResource::collection($invoices);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\Invoice::class);

        $validated = $request->validate([
            'customer_id' => 'required|uuid|exists:customers,id',
            'branch_id' => 'required|uuid|exists:branches,id',
            'invoice_number' => 'nullable|string|unique:invoices,invoice_number',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,paid,cancelled,overdue',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $invoice = $this->invoiceService->createInvoice($validated);

        return InvoiceResource::make($invoice)->response()->setStatusCode(201);
    }

    public function show(string $id): InvoiceResource
    {
        $invoice = \App\Models\Invoice::with(['customer', 'branch', 'items'])->findOrFail($id);
        $this->authorize('view', $invoice);

        return InvoiceResource::make($invoice);
    }

    public function update(Request $request, string $id): InvoiceResource
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        $this->authorize('update', $invoice);

        $validated = $request->validate([
            'customer_id' => 'sometimes|uuid|exists:customers,id',
            'branch_id' => 'sometimes|uuid|exists:branches,id',
            'issue_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'subtotal' => 'sometimes|numeric|min:0',
            'tax_amount' => 'sometimes|numeric|min:0',
            'discount_amount' => 'sometimes|numeric|min:0',
            'total' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,paid,cancelled,overdue',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array',
        ]);

        $updated = $this->invoiceService->updateInvoice($id, $validated);

        return InvoiceResource::make($updated);
    }

    public function destroy(string $id): JsonResponse
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        $this->authorize('delete', $invoice);

        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    public function cancel(Request $request, string $id): InvoiceResource
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        $this->authorize('cancel', $invoice);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $cancelled = $this->invoiceService->cancelInvoice($id, $validated['reason']);

        return InvoiceResource::make($cancelled);
    }

    public function markAsPaid(Request $request, string $id): InvoiceResource
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        $this->authorize('update', $invoice);

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money,other',
            'payment_date' => 'nullable|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $paid = $this->invoiceService->markAsPaid($id, $validated);

        return InvoiceResource::make($paid);
    }

    public function generatePdf(string $id): JsonResponse
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        $this->authorize('view', $invoice);

        $pdf = $this->invoiceService->generatePdf($id);

        return response()->json([
            'message' => 'PDF generation requested',
            'invoice' => $pdf,
        ]);
    }

    public function sendEmail(string $id): JsonResponse
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        $this->authorize('view', $invoice);

        $this->invoiceService->sendInvoiceEmail($id);

        return response()->json(['message' => 'Invoice email sent successfully']);
    }

    public function stats(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Invoice::class);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'nullable|uuid|exists:branches,id',
        ]);

        $stats = $this->invoiceService->getInvoiceStats(
            $validated['start_date'],
            $validated['end_date'],
            $validated['branch_id'] ?? null
        );

        return response()->json($stats);
    }
}
