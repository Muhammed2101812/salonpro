<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\Contracts\InvoiceServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private PaymentRepositoryInterface $paymentRepository
    ) {}

    public function createInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {
            $invoiceData = [
                'invoice_number' => $data['invoice_number'] ?? $this->generateInvoiceNumber(),
                'customer_id' => $data['customer_id'],
                'branch_id' => $data['branch_id'],
                'issue_date' => $data['issue_date'] ?? now(),
                'due_date' => $data['due_date'] ?? now()->addDays(30),
                'subtotal' => $data['subtotal'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'total' => $data['total'],
                'status' => $data['status'] ?? 'pending',
                'notes' => $data['notes'] ?? null,
            ];

            $invoice = $this->invoiceRepository->create($invoiceData);

            // Create invoice items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $invoice->items()->create($item);
                }
            }

            return $invoice->fresh(['items', 'customer', 'branch']);
        });
    }

    public function updateInvoice(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $invoice = $this->invoiceRepository->findOrFail($id);

            // Update invoice
            $invoice = $this->invoiceRepository->update($id, $data);

            // Update items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                $invoice->items()->delete();
                foreach ($data['items'] as $item) {
                    $invoice->items()->create($item);
                }
            }

            return $invoice->fresh(['items', 'customer', 'branch']);
        });
    }

    public function cancelInvoice(string $id, string $reason)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        if ($invoice->status === 'paid') {
            throw new \Exception('Cannot cancel a paid invoice');
        }

        return $this->invoiceRepository->update($id, [
            'status' => 'cancelled',
            'notes' => ($invoice->notes ?? '') . "\nCancelled: " . $reason,
            'cancelled_at' => now(),
        ]);
    }

    public function markAsPaid(string $id, array $paymentData)
    {
        return DB::transaction(function () use ($id, $paymentData) {
            $invoice = $this->invoiceRepository->findOrFail($id);

            // Create payment record
            $this->paymentRepository->create([
                'invoice_id' => $id,
                'customer_id' => $invoice->customer_id,
                'branch_id' => $invoice->branch_id,
                'amount' => $paymentData['amount'] ?? $invoice->total,
                'payment_method' => $paymentData['payment_method'],
                'payment_date' => $paymentData['payment_date'] ?? now(),
                'reference_number' => $paymentData['reference_number'] ?? null,
                'notes' => $paymentData['notes'] ?? null,
            ]);

            // Update invoice status
            return $this->invoiceRepository->update($id, [
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });
    }

    public function generatePdf(string $id)
    {
        $invoice = $this->invoiceRepository->with(['items', 'customer', 'branch'])->findOrFail($id);

        // TODO: Implement PDF generation logic using a package like dompdf or snappy
        // For now, return invoice data for PDF generation
        return $invoice;
    }

    public function sendInvoiceEmail(string $id)
    {
        $invoice = $this->invoiceRepository->with(['items', 'customer', 'branch'])->findOrFail($id);

        // TODO: Implement email sending logic
        // Mail::to($invoice->customer->email)->send(new InvoiceEmail($invoice));

        return true;
    }

    public function getCustomerInvoices(string $customerId, int $perPage = 15)
    {
        return $this->invoiceRepository->findByCustomer($customerId, $perPage);
    }

    public function getBranchInvoices(string $branchId, int $perPage = 15)
    {
        return $this->invoiceRepository->findByBranch($branchId, $perPage);
    }

    public function getInvoicesByStatus(string $status, int $perPage = 15)
    {
        return $this->invoiceRepository->findByStatus($status, $perPage);
    }

    public function getInvoiceStats(string $startDate, string $endDate, ?string $branchId = null)
    {
        return $this->invoiceRepository->getTotalsByPeriod($startDate, $endDate, $branchId);
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -4));

        return "{$prefix}-{$date}-{$random}";
    }
}
