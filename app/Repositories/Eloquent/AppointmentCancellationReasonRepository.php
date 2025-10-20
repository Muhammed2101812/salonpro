<?php

namespace App\Repositories\Eloquent;

use App\Models\AppointmentCancellationReason;
use App\Repositories\Contracts\AppointmentCancellationReasonRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentCancellationReasonRepository implements AppointmentCancellationReasonRepositoryInterface
{
    public function find(int $id): ?AppointmentCancellationReason
    {
        return AppointmentCancellationReason::find($id);
    }
    
    public function all(): Collection
    {
        return AppointmentCancellationReason::all();
    }
    
    public function create(array $data): AppointmentCancellationReason
    {
        return AppointmentCancellationReason::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $reason = $this->find($id);
        
        if (!$reason) {
            return false;
        }
        
        return $reason->update($data);
    }
    
    public function delete(int $id): bool
    {
        $reason = $this->find($id);
        
        if (!$reason) {
            return false;
        }
        
        return $reason->delete();
    }
    
    public function getActiveReasons(): Collection
    {
        return AppointmentCancellationReason::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->get();
    }
    
    public function getReasonsByBranch(int $branchId): Collection
    {
        return AppointmentCancellationReason::where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->get();
    }
    
    public function getMostUsedReasons(int $limit = 10): Collection
    {
        return AppointmentCancellationReason::where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
