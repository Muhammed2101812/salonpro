<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface InvoiceServiceInterface
{
    public function createInvoice(array $data);
    public function updateInvoice(string $id, array $data);
    public function cancelInvoice(string $id, string $reason);
    public function markAsPaid(string $id, array $paymentData);
    public function generatePdf(string $id);
    public function sendInvoiceEmail(string $id);
    public function getCustomerInvoices(string $customerId, int $perPage = 15);
    public function getBranchInvoices(string $branchId, int $perPage = 15);
    public function getInvoicesByStatus(string $status, int $perPage = 15);
    public function getInvoiceStats(string $startDate, string $endDate, ?string $branchId = null);
}
