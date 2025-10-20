<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeeCommission;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class EmployeeCommissionService
{
    /**
     * Get all commissions
     */
    public function getAllCommissions(): Collection
    {
        return EmployeeCommission::with(['employee', 'appointment', 'sale'])
            ->orderBy('commission_date', 'desc')
            ->get();
    }

    /**
     * Get commissions for a specific employee
     */
    public function getEmployeeCommissions(string $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = EmployeeCommission::with(['appointment', 'sale'])
            ->where('employee_id', $employeeId);

        if ($startDate) {
            $query->whereDate('commission_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('commission_date', '<=', $endDate);
        }

        return $query->orderBy('commission_date', 'desc')->get();
    }

    /**
     * Get unpaid commissions
     */
    public function getUnpaidCommissions(?string $employeeId = null): Collection
    {
        $query = EmployeeCommission::with(['employee', 'appointment', 'sale'])
            ->where('is_paid', false);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->orderBy('commission_date')->get();
    }

    /**
     * Get paid commissions
     */
    public function getPaidCommissions(?string $employeeId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = EmployeeCommission::with(['employee', 'appointment', 'sale'])
            ->where('is_paid', true);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }

        return $query->orderBy('paid_at', 'desc')->get();
    }

    /**
     * Create a new commission
     */
    public function createCommission(array $data): EmployeeCommission
    {
        // Validate commission rate
        if (isset($data['commission_rate'])) {
            $this->validateCommissionRate($data['commission_rate']);
        }

        // Calculate commission amount if not provided
        if (!isset($data['commission_amount']) && isset($data['amount']) && isset($data['commission_rate'])) {
            $data['commission_amount'] = $this->calculateCommissionAmount(
                $data['amount'],
                $data['commission_rate']
            );
        }

        // Set default values
        $data['is_paid'] = $data['is_paid'] ?? false;
        $data['commission_date'] = $data['commission_date'] ?? now();

        return EmployeeCommission::create($data);
    }

    /**
     * Update commission
     */
    public function updateCommission(string $id, array $data): bool
    {
        $commission = EmployeeCommission::find($id);

        if (!$commission) {
            throw new \Exception('Komisyon kaydı bulunamadı.');
        }

        // Prevent updating paid commissions
        if ($commission->is_paid && !isset($data['is_paid'])) {
            throw new \Exception('Ödenmiş komisyonlar güncellenemez.');
        }

        // Validate commission rate if provided
        if (isset($data['commission_rate'])) {
            $this->validateCommissionRate($data['commission_rate']);
        }

        // Recalculate commission amount if amount or rate changed
        if (isset($data['amount']) || isset($data['commission_rate'])) {
            $amount = $data['amount'] ?? $commission->amount;
            $rate = $data['commission_rate'] ?? $commission->commission_rate;
            $data['commission_amount'] = $this->calculateCommissionAmount($amount, $rate);
        }

        return $commission->update($data);
    }

    /**
     * Delete commission
     */
    public function deleteCommission(string $id): bool
    {
        $commission = EmployeeCommission::find($id);

        if (!$commission) {
            throw new \Exception('Komisyon kaydı bulunamadı.');
        }

        // Prevent deleting paid commissions
        if ($commission->is_paid) {
            throw new \Exception('Ödenmiş komisyonlar silinemez.');
        }

        return $commission->delete();
    }

    /**
     * Mark commission as paid
     */
    public function markAsPaid(string $id): bool
    {
        $commission = EmployeeCommission::find($id);

        if (!$commission) {
            throw new \Exception('Komisyon kaydı bulunamadı.');
        }

        if ($commission->is_paid) {
            throw new \Exception('Bu komisyon zaten ödenmiş.');
        }

        return $commission->update([
            'is_paid' => true,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark multiple commissions as paid
     */
    public function bulkMarkAsPaid(array $commissionIds): int
    {
        $count = 0;

        foreach ($commissionIds as $id) {
            try {
                $this->markAsPaid($id);
                $count++;
            } catch (\Exception $e) {
                // Skip already paid commissions
                continue;
            }
        }

        return $count;
    }

    /**
     * Calculate total unpaid commission for employee
     */
    public function getTotalUnpaidCommission(string $employeeId): float
    {
        return EmployeeCommission::where('employee_id', $employeeId)
            ->where('is_paid', false)
            ->sum('commission_amount');
    }

    /**
     * Get commission summary for employee
     */
    public function getEmployeeCommissionSummary(string $employeeId, Carbon $startDate, Carbon $endDate): array
    {
        $commissions = $this->getEmployeeCommissions($employeeId, $startDate, $endDate);

        $paid = $commissions->where('is_paid', true);
        $unpaid = $commissions->where('is_paid', false);

        return [
            'total_commissions' => $commissions->count(),
            'total_amount' => $commissions->sum('commission_amount'),
            'paid_count' => $paid->count(),
            'paid_amount' => $paid->sum('commission_amount'),
            'unpaid_count' => $unpaid->count(),
            'unpaid_amount' => $unpaid->sum('commission_amount'),
            'average_commission_rate' => round($commissions->avg('commission_rate'), 2),
            'total_sales_value' => $commissions->sum('amount'),
        ];
    }

    /**
     * Get commission statistics by period
     */
    public function getCommissionStatsByPeriod(string $employeeId, string $period = 'month'): array
    {
        $query = EmployeeCommission::where('employee_id', $employeeId)
            ->where('is_paid', true);

        $dateFormat = match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };

        return $query->get()
            ->groupBy(function ($commission) use ($period) {
                return match ($period) {
                    'day' => $commission->paid_at->format('Y-m-d'),
                    'week' => $commission->paid_at->format('Y-W'),
                    'month' => $commission->paid_at->format('Y-m'),
                    'year' => $commission->paid_at->format('Y'),
                    default => $commission->paid_at->format('Y-m'),
                };
            })
            ->map(function ($commissions, $period) {
                return [
                    'period' => $period,
                    'count' => $commissions->count(),
                    'total_amount' => $commissions->sum('commission_amount'),
                    'average_amount' => round($commissions->avg('commission_amount'), 2),
                ];
            })
            ->sortKeys()
            ->values()
            ->toArray();
    }

    /**
     * Get top earners
     */
    public function getTopEarners(int $limit = 10, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = EmployeeCommission::with('employee')
            ->where('is_paid', true);

        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }

        return $query->get()
            ->groupBy('employee_id')
            ->map(function ($commissions) {
                return [
                    'employee' => $commissions->first()->employee,
                    'total_commission' => $commissions->sum('commission_amount'),
                    'commission_count' => $commissions->count(),
                    'average_commission' => round($commissions->avg('commission_amount'), 2),
                    'total_sales' => $commissions->sum('amount'),
                ];
            })
            ->sortByDesc('total_commission')
            ->take($limit)
            ->values();
    }

    /**
     * Create commission from appointment
     */
    public function createFromAppointment(string $appointmentId, string $employeeId, float $amount, float $commissionRate): EmployeeCommission
    {
        return $this->createCommission([
            'employee_id' => $employeeId,
            'appointment_id' => $appointmentId,
            'amount' => $amount,
            'commission_rate' => $commissionRate,
        ]);
    }

    /**
     * Create commission from sale
     */
    public function createFromSale(string $saleId, string $employeeId, float $amount, float $commissionRate): EmployeeCommission
    {
        return $this->createCommission([
            'employee_id' => $employeeId,
            'sale_id' => $saleId,
            'amount' => $amount,
            'commission_rate' => $commissionRate,
        ]);
    }

    /**
     * Calculate commission amount
     */
    private function calculateCommissionAmount(float $amount, float $rate): float
    {
        return round($amount * ($rate / 100), 2);
    }

    /**
     * Validate commission rate
     */
    private function validateCommissionRate(float $rate): void
    {
        if ($rate < 0 || $rate > 100) {
            throw new \InvalidArgumentException(
                'Komisyon oranı 0 ile 100 arasında olmalıdır.'
            );
        }
    }
}
