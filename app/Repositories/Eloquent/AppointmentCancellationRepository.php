<?php

namespace App\Repositories\Eloquent;

use App\Models\AppointmentCancellation;
use App\Repositories\Contracts\AppointmentCancellationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentCancellationRepository implements AppointmentCancellationRepositoryInterface
{
    public function find(int $id): ?AppointmentCancellation
    {
        return AppointmentCancellation::find($id);
    }
    
    public function all(): Collection
    {
        return AppointmentCancellation::with(['appointment', 'reason', 'cancelledBy'])->get();
    }
    
    public function create(array $data): AppointmentCancellation
    {
        return AppointmentCancellation::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $cancellation = $this->find($id);
        
        if (!$cancellation) {
            return false;
        }
        
        return $cancellation->update($data);
    }
    
    public function delete(int $id): bool
    {
        $cancellation = $this->find($id);
        
        if (!$cancellation) {
            return false;
        }
        
        return $cancellation->delete();
    }
    
    public function getCancellationByAppointment(int $appointmentId): ?AppointmentCancellation
    {
        return AppointmentCancellation::where('appointment_id', $appointmentId)
            ->with(['reason', 'cancelledBy'])
            ->first();
    }
    
    public function getCancellationsByReason(int $reasonId): Collection
    {
        return AppointmentCancellation::where('cancellation_reason_id', $reasonId)
            ->with(['appointment', 'cancelledBy'])
            ->orderBy('cancelled_at', 'desc')
            ->get();
    }
    
    public function getCancellationsByCustomer(int $customerId): Collection
    {
        return AppointmentCancellation::whereHas('appointment', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
            ->with(['appointment', 'reason', 'cancelledBy'])
            ->orderBy('cancelled_at', 'desc')
            ->get();
    }
    
    public function getCancellationsByDateRange(string $startDate, string $endDate): Collection
    {
        return AppointmentCancellation::whereBetween('cancelled_at', [$startDate, $endDate])
            ->with(['appointment', 'reason', 'cancelledBy'])
            ->orderBy('cancelled_at', 'desc')
            ->get();
    }
    
    public function getRefundedCancellations(): Collection
    {
        return AppointmentCancellation::where('refund_issued', true)
            ->with(['appointment', 'reason', 'cancelledBy'])
            ->orderBy('cancelled_at', 'desc')
            ->get();
    }
}
