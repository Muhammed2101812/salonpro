<?php

namespace App\Repositories\Eloquent;

use App\Models\AppointmentGroup;
use App\Repositories\Contracts\AppointmentGroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentGroupRepository implements AppointmentGroupRepositoryInterface
{
    public function find(int $id): ?AppointmentGroup
    {
        return AppointmentGroup::find($id);
    }
    
    public function all(): Collection
    {
        return AppointmentGroup::with(['branch', 'service', 'employee'])->get();
    }
    
    public function create(array $data): AppointmentGroup
    {
        return AppointmentGroup::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $group = $this->find($id);
        
        if (!$group) {
            return false;
        }
        
        return $group->update($data);
    }
    
    public function delete(int $id): bool
    {
        $group = $this->find($id);
        
        if (!$group) {
            return false;
        }
        
        return $group->delete();
    }
    
    public function getGroupsByBranch(int $branchId): Collection
    {
        return AppointmentGroup::where('branch_id', $branchId)
            ->with(['service', 'employee'])
            ->get();
    }
    
    public function getGroupsByService(int $serviceId): Collection
    {
        return AppointmentGroup::where('service_id', $serviceId)
            ->with(['branch', 'employee'])
            ->get();
    }
    
    public function getUpcomingGroups(): Collection
    {
        return AppointmentGroup::where('scheduled_at', '>', now())
            ->where('status', '!=', 'cancelled')
            ->with(['branch', 'service', 'employee'])
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }
    
    public function getGroupsWithAvailableSpots(): Collection
    {
        return AppointmentGroup::whereRaw('current_participants < max_participants')
            ->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->with(['branch', 'service', 'employee'])
            ->get();
    }
}
