<?php

namespace App\Repositories\Eloquent;

use App\Models\AppointmentWaitlist;
use App\Repositories\Contracts\AppointmentWaitlistRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentWaitlistRepository implements AppointmentWaitlistRepositoryInterface
{
    public function find(int $id): ?AppointmentWaitlist
    {
        return AppointmentWaitlist::find($id);
    }
    
    public function all(): Collection
    {
        return AppointmentWaitlist::with(['branch', 'customer', 'service', 'employee'])->get();
    }
    
    public function create(array $data): AppointmentWaitlist
    {
        return AppointmentWaitlist::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $waitlist = $this->find($id);
        
        if (!$waitlist) {
            return false;
        }
        
        return $waitlist->update($data);
    }
    
    public function delete(int $id): bool
    {
        $waitlist = $this->find($id);
        
        if (!$waitlist) {
            return false;
        }
        
        return $waitlist->delete();
    }
    
    public function getWaitlistByBranch(int $branchId): Collection
    {
        return AppointmentWaitlist::where('branch_id', $branchId)
            ->with(['customer', 'service', 'employee'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    public function getWaitlistByCustomer(int $customerId): Collection
    {
        return AppointmentWaitlist::where('customer_id', $customerId)
            ->with(['branch', 'service', 'employee'])
            ->get();
    }
    
    public function getActiveWaitlist(): Collection
    {
        return AppointmentWaitlist::where('status', 'waiting')
            ->with(['branch', 'customer', 'service', 'employee'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    public function getWaitlistByService(int $serviceId): Collection
    {
        return AppointmentWaitlist::where('service_id', $serviceId)
            ->with(['branch', 'customer', 'employee'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    public function getWaitlistByDateRange(string $startDate, string $endDate): Collection
    {
        return AppointmentWaitlist::whereBetween('preferred_date', [$startDate, $endDate])
            ->with(['branch', 'customer', 'service', 'employee'])
            ->orderBy('preferred_date', 'asc')
            ->get();
    }
}
