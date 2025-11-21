<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\StockAlertRepositoryInterface;
use App\Services\Contracts\StockAlertServiceInterface;
use Illuminate\Support\Facades\DB;

class StockAlertService extends BaseService implements StockAlertServiceInterface
{
    public function __construct(
        protected StockAlertRepositoryInterface $stockAlertRepository
    ) {
        parent::__construct($stockAlertRepository);
    }

    public function getByBranch(string $branchId, int $perPage = 15): mixed
    {
        return $this->stockAlertRepository->findByBranch($branchId, $perPage);
    }

    public function getByProduct(string $productId): mixed
    {
        return $this->stockAlertRepository->findByProduct($productId);
    }

    public function getActive(?string $branchId = null): mixed
    {
        return $this->stockAlertRepository->getActive($branchId);
    }

    public function getResolved(?string $branchId = null): mixed
    {
        return $this->stockAlertRepository->getResolved($branchId);
    }

    public function getCritical(?string $branchId = null): mixed
    {
        return $this->stockAlertRepository->getCriticalAlerts($branchId);
    }

    public function markAsNotified(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $alert = $this->stockAlertRepository->findOrFail($id);

            if ($alert->notified_at) {
                throw new \RuntimeException('Alert already marked as notified');
            }

            return $this->stockAlertRepository->markAsNotified($id);
        });
    }

    public function resolve(string $id, ?string $notes = null): mixed
    {
        return DB::transaction(function () use ($id, $notes) {
            $alert = $this->stockAlertRepository->findOrFail($id);

            if ($alert->status === 'resolved') {
                throw new \RuntimeException('Alert already resolved');
            }

            return $this->stockAlertRepository->markAsResolved($id, $notes);
        });
    }

    public function createFromStockCheck(array $data): mixed
    {
        return DB::transaction(function () use ($data) {
            // Check if active alert already exists for this product
            $existing = $this->stockAlertRepository->model
                ->where('branch_id', $data['branch_id'])
                ->where('product_id', $data['product_id'])
                ->where('status', 'active')
                ->whereNull('resolved_at')
                ->first();

            if ($existing) {
                // Update existing alert
                return $this->stockAlertRepository->update($existing->id, [
                    'current_quantity' => $data['current_quantity'],
                    'priority' => $data['priority'] ?? $existing->priority,
                    'notes' => $data['notes'] ?? $existing->notes,
                ]);
            }

            // Create new alert
            $data['status'] = 'active';
            return $this->stockAlertRepository->create($data);
        });
    }
}
