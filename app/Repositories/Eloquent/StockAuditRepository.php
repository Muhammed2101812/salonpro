<?php

namespace App\Repositories\Eloquent;

use App\Models\StockAudit;
use App\Repositories\Contracts\StockAuditRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StockAuditRepository implements StockAuditRepositoryInterface
{
    public function __construct(
        protected StockAudit $model
    ) {}

    public function all(): Collection
    {
        return $this->model
            ->with(['branch', 'creator', 'items.product'])
            ->latest()
            ->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->with(['branch', 'creator', 'items']);

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('audit_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('audit_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('audit_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('notes', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?StockAudit
    {
        return $this->model
            ->with(['branch', 'creator', 'items.product'])
            ->find($id);
    }

    public function create(array $data): StockAudit
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            $audit = $this->model->create($data);

            if (!empty($items)) {
                foreach ($items as $item) {
                    $audit->items()->create($item);
                }
            }

            return $audit->load(['branch', 'creator', 'items.product']);
        });
    }

    public function update(int $id, array $data): StockAudit
    {
        return DB::transaction(function () use ($id, $data) {
            $audit = $this->findById($id);
            
            if (!$audit) {
                throw new \Exception('Stok sayımı bulunamadı.');
            }

            $items = $data['items'] ?? null;
            unset($data['items']);

            $audit->update($data);

            if ($items !== null) {
                $audit->items()->delete();
                foreach ($items as $item) {
                    $audit->items()->create($item);
                }
            }

            return $audit->fresh(['branch', 'creator', 'items.product']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $audit = $this->findById($id);
            
            if (!$audit) {
                throw new \Exception('Stok sayımı bulunamadı.');
            }

            if ($audit->status === 'completed') {
                throw new \Exception('Tamamlanmış stok sayımı silinemez.');
            }

            $audit->items()->delete();
            return $audit->delete();
        });
    }

    public function findByBranch(int $branchId): Collection
    {
        return $this->model
            ->with(['creator', 'items.product'])
            ->where('branch_id', $branchId)
            ->latest()
            ->get();
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model
            ->with(['branch', 'creator', 'items.product'])
            ->where('status', $status)
            ->latest()
            ->get();
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->with(['branch', 'creator', 'items.product'])
            ->whereDate('audit_date', '>=', $startDate)
            ->whereDate('audit_date', '<=', $endDate)
            ->latest()
            ->get();
    }

    public function findPending(): Collection
    {
        return $this->model
            ->with(['branch', 'creator', 'items.product'])
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    public function complete(int $id, array $data): StockAudit
    {
        return DB::transaction(function () use ($id, $data) {
            $audit = $this->findById($id);
            
            if (!$audit) {
                throw new \Exception('Stok sayımı bulunamadı.');
            }

            if ($audit->status === 'completed') {
                throw new \Exception('Stok sayımı zaten tamamlanmış.');
            }

            $audit->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => auth()->id(),
                'notes' => $data['notes'] ?? $audit->notes,
            ]);

            // Apply stock adjustments if specified
            if (!empty($data['apply_adjustments'])) {
                foreach ($audit->items as $item) {
                    if ($item->difference != 0) {
                        $item->product->increment('quantity', $item->difference);
                    }
                }
            }

            return $audit->fresh(['branch', 'creator', 'items.product']);
        });
    }

    public function cancel(int $id, string $reason): StockAudit
    {
        $audit = $this->findById($id);
        
        if (!$audit) {
            throw new \Exception('Stok sayımı bulunamadı.');
        }

        if ($audit->status === 'completed') {
            throw new \Exception('Tamamlanmış stok sayımı iptal edilemez.');
        }

        $audit->update([
            'status' => 'cancelled',
            'notes' => ($audit->notes ? $audit->notes . "\n\n" : '') . "İptal Nedeni: " . $reason,
        ]);

        return $audit->fresh(['branch', 'creator', 'items.product']);
    }
}
