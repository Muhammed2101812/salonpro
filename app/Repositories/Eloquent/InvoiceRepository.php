<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    public function findByCustomer(string $customerId, int $perPage = 15)
    {
        return $this->model->where('customer_id', $customerId)
            ->with(['customer', 'branch', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByBranch(string $branchId, int $perPage = 15)
    {
        return $this->model->where('branch_id', $branchId)
            ->with(['customer', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByStatus(string $status, int $perPage = 15)
    {
        return $this->model->where('status', $status)
            ->with(['customer', 'branch', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getTotalsByPeriod(string $startDate, string $endDate, ?string $branchId = null)
    {
        $query = $this->model->whereBetween('issue_date', [$startDate, $endDate]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return [
            'total_invoices' => $query->count(),
            'total_amount' => $query->sum('total'),
            'paid_amount' => $query->where('status', 'paid')->sum('total'),
            'pending_amount' => $query->where('status', 'pending')->sum('total'),
        ];
    }
}
