<?php

namespace App\Repositories\Eloquent;

use App\Models\AppointmentConflict;
use App\Repositories\Contracts\AppointmentConflictRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentConflictRepository implements AppointmentConflictRepositoryInterface
{
    public function find(int $id): ?AppointmentConflict
    {
        return AppointmentConflict::find($id);
    }
    
    public function all(): Collection
    {
        return AppointmentConflict::with(['appointment', 'conflictingAppointment', 'resolvedBy'])->get();
    }
    
    public function create(array $data): AppointmentConflict
    {
        return AppointmentConflict::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $conflict = $this->find($id);
        
        if (!$conflict) {
            return false;
        }
        
        return $conflict->update($data);
    }
    
    public function delete(int $id): bool
    {
        $conflict = $this->find($id);
        
        if (!$conflict) {
            return false;
        }
        
        return $conflict->delete();
    }
    
    public function getConflictsByAppointment(int $appointmentId): Collection
    {
        return AppointmentConflict::where('appointment_id', $appointmentId)
            ->orWhere('conflicting_appointment_id', $appointmentId)
            ->with(['appointment', 'conflictingAppointment', 'resolvedBy'])
            ->get();
    }
    
    public function getUnresolvedConflicts(): Collection
    {
        return AppointmentConflict::whereNull('resolved_at')
            ->with(['appointment', 'conflictingAppointment'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function getResolvedConflicts(): Collection
    {
        return AppointmentConflict::whereNotNull('resolved_at')
            ->with(['appointment', 'conflictingAppointment', 'resolvedBy'])
            ->orderBy('resolved_at', 'desc')
            ->get();
    }
    
    public function getConflictsByType(string $type): Collection
    {
        return AppointmentConflict::where('conflict_type', $type)
            ->with(['appointment', 'conflictingAppointment', 'resolvedBy'])
            ->get();
    }
}
