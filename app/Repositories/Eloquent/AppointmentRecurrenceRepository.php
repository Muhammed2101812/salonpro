<?php

namespace App\Repositories\Eloquent;

use App\Models\AppointmentRecurrence;
use App\Repositories\Contracts\AppointmentRecurrenceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRecurrenceRepository implements AppointmentRecurrenceRepositoryInterface
{
    public function find(int $id): ?AppointmentRecurrence
    {
        return AppointmentRecurrence::find($id);
    }
    
    public function findByAppointment(int $appointmentId): ?AppointmentRecurrence
    {
        return AppointmentRecurrence::where('appointment_id', $appointmentId)->first();
    }
    
    public function all(): Collection
    {
        return AppointmentRecurrence::with(['appointment'])->get();
    }
    
    public function create(array $data): AppointmentRecurrence
    {
        return AppointmentRecurrence::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $recurrence = $this->find($id);
        
        if (!$recurrence) {
            return false;
        }
        
        return $recurrence->update($data);
    }
    
    public function delete(int $id): bool
    {
        $recurrence = $this->find($id);
        
        if (!$recurrence) {
            return false;
        }
        
        return $recurrence->delete();
    }
    
    public function getActiveRecurrences(): Collection
    {
        return AppointmentRecurrence::where(function ($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
        })->with(['appointment'])->get();
    }
    
    public function getRecurrencesByPattern(string $pattern): Collection
    {
        return AppointmentRecurrence::where('pattern', $pattern)
            ->with(['appointment'])
            ->get();
    }
    
    public function getRecurrencesByDateRange(string $startDate, string $endDate): Collection
    {
        return AppointmentRecurrence::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where(function ($q2) use ($endDate) {
                            $q2->whereNull('end_date')
                               ->orWhere('end_date', '>=', $endDate);
                        });
                  });
        })->with(['appointment'])->get();
    }
}
