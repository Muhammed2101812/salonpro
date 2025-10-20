<?php

namespace App\Repositories\Eloquent;

use App\Models\AppointmentReminder;
use App\Repositories\Contracts\AppointmentReminderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentReminderRepository implements AppointmentReminderRepositoryInterface
{
    public function find(int $id): ?AppointmentReminder
    {
        return AppointmentReminder::find($id);
    }
    
    public function all(): Collection
    {
        return AppointmentReminder::with(['appointment'])->get();
    }
    
    public function create(array $data): AppointmentReminder
    {
        return AppointmentReminder::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $reminder = $this->find($id);
        
        if (!$reminder) {
            return false;
        }
        
        return $reminder->update($data);
    }
    
    public function delete(int $id): bool
    {
        $reminder = $this->find($id);
        
        if (!$reminder) {
            return false;
        }
        
        return $reminder->delete();
    }
    
    public function getRemindersByAppointment(int $appointmentId): Collection
    {
        return AppointmentReminder::where('appointment_id', $appointmentId)
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }
    
    public function getPendingReminders(): Collection
    {
        return AppointmentReminder::where('status', 'pending')
            ->with(['appointment'])
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }
    
    public function getSentReminders(): Collection
    {
        return AppointmentReminder::where('status', 'sent')
            ->with(['appointment'])
            ->orderBy('sent_at', 'desc')
            ->get();
    }
    
    public function getRemindersByType(string $type): Collection
    {
        return AppointmentReminder::where('reminder_type', $type)
            ->with(['appointment'])
            ->get();
    }
    
    public function getRemindersDueForSending(): Collection
    {
        return AppointmentReminder::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->with(['appointment'])
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }
}
