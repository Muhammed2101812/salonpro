<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeePerformance;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class EmployeePerformanceService
{
    /**
     * Get all performance records
     */
    public function getAllPerformances(): Collection
    {
        return EmployeePerformance::with('employee')
            ->orderBy('evaluation_date', 'desc')
            ->get();
    }

    /**
     * Get performance records for a specific employee
     */
    public function getEmployeePerformances(string $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = EmployeePerformance::where('employee_id', $employeeId);

        if ($startDate) {
            $query->whereDate('evaluation_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('evaluation_date', '<=', $endDate);
        }

        return $query->orderBy('evaluation_date', 'desc')->get();
    }

    /**
     * Get latest performance for employee
     */
    public function getLatestPerformance(string $employeeId): ?EmployeePerformance
    {
        return EmployeePerformance::where('employee_id', $employeeId)
            ->orderBy('evaluation_date', 'desc')
            ->first();
    }

    /**
     * Create a new performance record
     */
    public function createPerformance(array $data): EmployeePerformance
    {
        // Validate scores
        $this->validateScores($data);

        // Calculate average if not provided
        if (!isset($data['average_score'])) {
            $data['average_score'] = $this->calculateAverageScore($data);
        }

        return EmployeePerformance::create($data);
    }

    /**
     * Update performance record
     */
    public function updatePerformance(string $id, array $data): bool
    {
        $performance = EmployeePerformance::find($id);

        if (!$performance) {
            throw new \Exception('Performans kaydı bulunamadı.');
        }

        // Validate scores if provided
        if ($this->hasScoreData($data)) {
            $this->validateScores($data);
            $data['average_score'] = $this->calculateAverageScore(array_merge($performance->toArray(), $data));
        }

        return $performance->update($data);
    }

    /**
     * Delete performance record
     */
    public function deletePerformance(string $id): bool
    {
        return EmployeePerformance::where('id', $id)->delete();
    }

    /**
     * Get performance statistics for employee
     */
    public function getEmployeePerformanceStats(string $employeeId, Carbon $startDate, Carbon $endDate): array
    {
        $performances = $this->getEmployeePerformances($employeeId, $startDate, $endDate);

        if ($performances->isEmpty()) {
            return [
                'total_evaluations' => 0,
                'average_customer_satisfaction' => 0,
                'average_punctuality' => 0,
                'average_sales_performance' => 0,
                'average_teamwork' => 0,
                'overall_average' => 0,
                'total_sales' => 0,
                'total_appointments' => 0,
                'trend' => 'stable',
            ];
        }

        $latest = $performances->first();
        $previous = $performances->skip(1)->first();

        return [
            'total_evaluations' => $performances->count(),
            'average_customer_satisfaction' => round($performances->avg('customer_satisfaction_score'), 2),
            'average_punctuality' => round($performances->avg('punctuality_score'), 2),
            'average_sales_performance' => round($performances->avg('sales_performance_score'), 2),
            'average_teamwork' => round($performances->avg('teamwork_score'), 2),
            'overall_average' => round($performances->avg(fn($p) => $p->getAverageScore()), 2),
            'total_sales' => $performances->sum('total_sales'),
            'total_appointments' => $performances->sum('total_appointments'),
            'trend' => $this->calculateTrend($latest, $previous),
        ];
    }

    /**
     * Get top performers
     */
    public function getTopPerformers(int $limit = 10, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = EmployeePerformance::with('employee');

        if ($startDate) {
            $query->whereDate('evaluation_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('evaluation_date', '<=', $endDate);
        }

        return $query->get()
            ->groupBy('employee_id')
            ->map(function ($performances) {
                $avgScore = $performances->avg(fn($p) => $p->getAverageScore());
                $latest = $performances->first();

                return [
                    'employee' => $latest->employee,
                    'average_score' => round($avgScore, 2),
                    'evaluations_count' => $performances->count(),
                    'total_sales' => $performances->sum('total_sales'),
                    'total_appointments' => $performances->sum('total_appointments'),
                ];
            })
            ->sortByDesc('average_score')
            ->take($limit)
            ->values();
    }

    /**
     * Get employees needing improvement
     */
    public function getEmployeesNeedingImprovement(float $threshold = 60.0): Collection
    {
        return EmployeePerformance::with('employee')
            ->get()
            ->groupBy('employee_id')
            ->filter(function ($performances) use ($threshold) {
                $avgScore = $performances->avg(fn($p) => $p->getAverageScore());
                return $avgScore < $threshold;
            })
            ->map(function ($performances) {
                $avgScore = $performances->avg(fn($p) => $p->getAverageScore());
                $latest = $performances->first();

                return [
                    'employee' => $latest->employee,
                    'average_score' => round($avgScore, 2),
                    'evaluations_count' => $performances->count(),
                    'areas_to_improve' => $this->identifyAreasToImprove($performances),
                ];
            })
            ->values();
    }

    /**
     * Compare employee performance with team average
     */
    public function compareWithTeamAverage(string $employeeId, Carbon $startDate, Carbon $endDate): array
    {
        $employeePerfs = $this->getEmployeePerformances($employeeId, $startDate, $endDate);
        $teamPerfs = EmployeePerformance::whereDate('evaluation_date', '>=', $startDate)
            ->whereDate('evaluation_date', '<=', $endDate)
            ->get();

        $employeeAvg = $employeePerfs->avg(fn($p) => $p->getAverageScore());
        $teamAvg = $teamPerfs->avg(fn($p) => $p->getAverageScore());

        return [
            'employee_average' => round($employeeAvg ?? 0, 2),
            'team_average' => round($teamAvg ?? 0, 2),
            'difference' => round(($employeeAvg ?? 0) - ($teamAvg ?? 0), 2),
            'performance_level' => $this->getPerformanceLevel($employeeAvg ?? 0, $teamAvg ?? 0),
        ];
    }

    /**
     * Get performance trend for employee
     */
    public function getPerformanceTrend(string $employeeId, int $months = 6): array
    {
        $performances = EmployeePerformance::where('employee_id', $employeeId)
            ->whereDate('evaluation_date', '>=', now()->subMonths($months))
            ->orderBy('evaluation_date')
            ->get();

        return $performances->map(function ($performance) {
            return [
                'date' => $performance->evaluation_date->format('Y-m'),
                'score' => $performance->getAverageScore(),
                'customer_satisfaction' => $performance->customer_satisfaction_score,
                'punctuality' => $performance->punctuality_score,
                'sales_performance' => $performance->sales_performance_score,
                'teamwork' => $performance->teamwork_score,
            ];
        })->toArray();
    }

    /**
     * Validate scores
     */
    private function validateScores(array $data): void
    {
        $scoreFields = [
            'customer_satisfaction_score',
            'punctuality_score',
            'sales_performance_score',
            'teamwork_score',
        ];

        foreach ($scoreFields as $field) {
            if (isset($data[$field])) {
                $score = $data[$field];
                if ($score < 0 || $score > 100) {
                    throw new \InvalidArgumentException(
                        "Performans skorları 0 ile 100 arasında olmalıdır."
                    );
                }
            }
        }
    }

    /**
     * Calculate average score
     */
    private function calculateAverageScore(array $data): float
    {
        $scores = array_filter([
            $data['customer_satisfaction_score'] ?? null,
            $data['punctuality_score'] ?? null,
            $data['sales_performance_score'] ?? null,
            $data['teamwork_score'] ?? null,
        ]);

        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
    }

    /**
     * Check if data has score information
     */
    private function hasScoreData(array $data): bool
    {
        return isset($data['customer_satisfaction_score'])
            || isset($data['punctuality_score'])
            || isset($data['sales_performance_score'])
            || isset($data['teamwork_score']);
    }

    /**
     * Calculate trend between two performance records
     */
    private function calculateTrend(?EmployeePerformance $latest, ?EmployeePerformance $previous): string
    {
        if (!$latest || !$previous) {
            return 'stable';
        }

        $latestScore = $latest->getAverageScore();
        $previousScore = $previous->getAverageScore();

        $difference = $latestScore - $previousScore;

        if ($difference > 5) {
            return 'improving';
        } elseif ($difference < -5) {
            return 'declining';
        }

        return 'stable';
    }

    /**
     * Identify areas that need improvement
     */
    private function identifyAreasToImprove(Collection $performances): array
    {
        $avgScores = [
            'customer_satisfaction' => $performances->avg('customer_satisfaction_score'),
            'punctuality' => $performances->avg('punctuality_score'),
            'sales_performance' => $performances->avg('sales_performance_score'),
            'teamwork' => $performances->avg('teamwork_score'),
        ];

        return collect($avgScores)
            ->filter(fn($score) => $score < 70)
            ->sortBy(fn($score) => $score)
            ->keys()
            ->toArray();
    }

    /**
     * Get performance level
     */
    private function getPerformanceLevel(float $employeeAvg, float $teamAvg): string
    {
        $difference = $employeeAvg - $teamAvg;

        if ($difference > 10) {
            return 'excellent';
        } elseif ($difference > 0) {
            return 'above_average';
        } elseif ($difference > -10) {
            return 'average';
        } else {
            return 'below_average';
        }
    }
}
