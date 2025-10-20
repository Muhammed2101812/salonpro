<?php

namespace App\Repositories\Eloquent;

use App\Models\AppointmentGroupParticipant;
use App\Repositories\Contracts\AppointmentGroupParticipantRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentGroupParticipantRepository implements AppointmentGroupParticipantRepositoryInterface
{
    public function find(int $id): ?AppointmentGroupParticipant
    {
        return AppointmentGroupParticipant::find($id);
    }
    
    public function all(): Collection
    {
        return AppointmentGroupParticipant::with(['group', 'customer', 'appointment'])->get();
    }
    
    public function create(array $data): AppointmentGroupParticipant
    {
        return AppointmentGroupParticipant::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $participant = $this->find($id);
        
        if (!$participant) {
            return false;
        }
        
        return $participant->update($data);
    }
    
    public function delete(int $id): bool
    {
        $participant = $this->find($id);
        
        if (!$participant) {
            return false;
        }
        
        return $participant->delete();
    }
    
    public function getParticipantsByGroup(int $groupId): Collection
    {
        return AppointmentGroupParticipant::where('appointment_group_id', $groupId)
            ->with(['customer', 'appointment'])
            ->get();
    }
    
    public function getParticipantsByCustomer(int $customerId): Collection
    {
        return AppointmentGroupParticipant::where('customer_id', $customerId)
            ->with(['group', 'appointment'])
            ->get();
    }
    
    public function getConfirmedParticipants(int $groupId): Collection
    {
        return AppointmentGroupParticipant::where('appointment_group_id', $groupId)
            ->where('status', 'confirmed')
            ->with(['customer', 'appointment'])
            ->get();
    }
    
    public function getPendingParticipants(int $groupId): Collection
    {
        return AppointmentGroupParticipant::where('appointment_group_id', $groupId)
            ->where('status', 'pending')
            ->with(['customer', 'appointment'])
            ->get();
    }
}
