<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\AppointmentCancellation;
use App\Repositories\Contracts\AppointmentCancellationRepositoryInterface;

class AppointmentCancellationRepository extends BaseRepository implements AppointmentCancellationRepositoryInterface
{
    public function __construct(AppointmentCancellation $model)
    {
        parent::__construct($model);
    }

    public function findByAppointment(string $appointmentId)
    {
        return $this->model->where('appointment_id', $appointmentId)
            ->with(['appointment', 'reason', 'cancelledByUser'])
            ->first();
    }

    public function getStatsByPeriod(string $startDate, string $endDate, ?string $branchId = null)
    {
        $query = $this->model->whereBetween('cancelled_at', [$startDate, $endDate]);

        if ($branchId) {
            $query->whereHas('appointment', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return [
            'total_cancellations' => $query->count(),
            'total_refund' => $query->sum('refund_amount'),
            'by_type' => $query->groupBy('cancelled_by_type')->selectRaw('cancelled_by_type, count(*) as count')->get(),
        ];
    }

    public function getTopCancellationReasons(int $limit = 10)
    {
        return $this->model->select('cancellation_reason_id')
            ->with('reason')
            ->groupBy('cancellation_reason_id')
            ->selectRaw('cancellation_reason_id, count(*) as count')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }
}
