<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeePerformance;
use App\Repositories\Contracts\EmployeePerformanceRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeePerformanceRepository implements EmployeePerformanceRepositoryInterface
{
    public function all(): Collection
    {
        return EmployeePerformance::with('employee')->get();
    }

    public function find(string $id): ?EmployeePerformance
    {
        return EmployeePerformance::with('employee')->find($id);
    }

    public function create(array $data): EmployeePerformance
    {
        return EmployeePerformance::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return EmployeePerformance::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return EmployeePerformance::where('id', $id)->delete();
    }

    public function getByEmployee(string $employeeId): Collection
    {
        return EmployeePerformance::where('employee_id', $employeeId)
            ->orderBy('evaluation_date', 'desc')
            ->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return EmployeePerformance::with('employee')
            ->whereBetween('evaluation_date', [$startDate, $endDate])
            ->orderBy('evaluation_date', 'desc')
            ->get();
    }

    public function getLatestEvaluation(string $employeeId): ?EmployeePerformance
    {
        return EmployeePerformance::where('employee_id', $employeeId)
            ->orderBy('evaluation_date', 'desc')
            ->first();
    }

    public function getTopPerformers(int $limit = 10): Collection
    {
        return EmployeePerformance::with('employee')
            ->select('employee_id')
            ->selectRaw('AVG((customer_satisfaction_score + punctuality_score + sales_performance_score + teamwork_score) / 4) as avg_score')
            ->groupBy('employee_id')
            ->orderBy('avg_score', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAverageScores(string $employeeId): array
    {
        $averages = EmployeePerformance::where('employee_id', $employeeId)
            ->selectRaw('
                AVG(customer_satisfaction_score) as avg_customer_satisfaction,
                AVG(punctuality_score) as avg_punctuality,
                AVG(sales_performance_score) as avg_sales_performance,
                AVG(teamwork_score) as avg_teamwork,
                AVG((customer_satisfaction_score + punctuality_score + sales_performance_score + teamwork_score) / 4) as avg_overall
            ')
            ->first();

        return [
            'customer_satisfaction' => round($averages->avg_customer_satisfaction ?? 0, 2),
            'punctuality' => round($averages->avg_punctuality ?? 0, 2),
            'sales_performance' => round($averages->avg_sales_performance ?? 0, 2),
            'teamwork' => round($averages->avg_teamwork ?? 0, 2),
            'overall' => round($averages->avg_overall ?? 0, 2),
        ];
    }
}
