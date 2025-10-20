<?php

namespace App\Services;

use App\Repositories\Contracts\StockAuditRepositoryInterface;
use App\Models\StockAudit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class StockAuditService
{
    public function __construct(
        protected StockAuditRepositoryInterface $repository
    ) {}

    public function getAllAudits(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginatedAudits(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getAuditById(int $id): ?StockAudit
    {
        return $this->repository->findById($id);
    }

    public function createAudit(array $data): StockAudit
    {
        $data['created_by'] = Auth::id();
        $data['status'] = $data['status'] ?? 'pending';
        $data['audit_number'] = $this->generateAuditNumber();
        
        return $this->repository->create($data);
    }

    public function updateAudit(int $id, array $data): StockAudit
    {
        $audit = $this->repository->findById($id);
        
        if (!$audit) {
            throw new \Exception('Stok sayımı bulunamadı.');
        }

        if ($audit->status === 'completed') {
            throw new \Exception('Tamamlanmış stok sayımı güncellenemez.');
        }

        return $this->repository->update($id, $data);
    }

    public function deleteAudit(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getAuditsByBranch(int $branchId): Collection
    {
        return $this->repository->findByBranch($branchId);
    }

    public function getAuditsByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function getAuditsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function getPendingAudits(): Collection
    {
        return $this->repository->findPending();
    }

    public function completeAudit(int $id, array $data): StockAudit
    {
        return $this->repository->complete($id, $data);
    }

    public function cancelAudit(int $id, string $reason): StockAudit
    {
        return $this->repository->cancel($id, $reason);
    }

    public function calculateVariances(int $id): array
    {
        $audit = $this->repository->findById($id);
        
        if (!$audit) {
            throw new \Exception('Stok sayımı bulunamadı.');
        }

        $totalItems = $audit->items->count();
        $totalVariance = 0;
        $totalValueVariance = 0;
        $itemsWithVariance = 0;

        foreach ($audit->items as $item) {
            if ($item->difference != 0) {
                $itemsWithVariance++;
                $totalVariance += abs($item->difference);
                $totalValueVariance += ($item->difference * $item->product->price);
            }
        }

        return [
            'total_items' => $totalItems,
            'items_with_variance' => $itemsWithVariance,
            'variance_percentage' => $totalItems > 0 ? round(($itemsWithVariance / $totalItems) * 100, 2) : 0,
            'total_variance' => $totalVariance,
            'total_value_variance' => round($totalValueVariance, 2),
        ];
    }

    protected function generateAuditNumber(): string
    {
        $prefix = 'SA';
        $date = now()->format('Ymd');
        $lastAudit = StockAudit::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastAudit ? (intval(substr($lastAudit->audit_number, -4)) + 1) : 1;
        
        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
