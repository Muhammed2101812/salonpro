<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\EmployeeCommissionRepositoryInterface;
use App\Services\Contracts\EmployeeCommissionServiceInterface;
use Illuminate\Support\Facades\DB;

class EmployeeCommissionService extends BaseService implements EmployeeCommissionServiceInterface
{
    public function __construct(
        protected EmployeeCommissionRepositoryInterface $commissionRepository
    ) {
        parent::__construct($commissionRepository);
    }

    public function getByEmployee(string $employeeId, int $perPage = 15): mixed
    {
        return $this->commissionRepository->findByEmployee($employeeId, $perPage);
    }

    public function getUnpaid(?string $employeeId = null): mixed
    {
        return $this->commissionRepository->findUnpaid($employeeId);
    }

    public function markAsPaid(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $commission = $this->commissionRepository->findOrFail($id);

            if ($commission->payment_status === 'paid') {
                throw new \RuntimeException('Commission already paid');
            }

            return $this->commissionRepository->update($id, [
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
        });
    }

    public function markMultipleAsPaid(array $ids): array
    {
        return DB::transaction(function () use ($ids) {
            $results = [];

            foreach ($ids as $id) {
                try {
                    $results[$id] = $this->markAsPaid($id);
                } catch (\Exception $e) {
                    $results[$id] = ['error' => $e->getMessage()];
                }
            }

            return $results;
        });
    }

    public function getSummary(string $employeeId, string $startDate, string $endDate): array
    {
        return $this->commissionRepository->getSummary($employeeId, $startDate, $endDate);
    }

    public function calculateCommission(float $baseAmount, float $commissionRate): float
    {
        return round($baseAmount * ($commissionRate / 100), 2);
    }
}
