<?php

namespace App\Repositories\Contracts;

use App\Models\AppointmentRecurrence;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentRecurrenceRepositoryInterface
{
    public function find(int $id): ?AppointmentRecurrence;
    
    public function findByAppointment(int $appointmentId): ?AppointmentRecurrence;
    
    public function all(): Collection;
    
    public function create(array $data): AppointmentRecurrence;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getActiveRecurrences(): Collection;
    
    public function getRecurrencesByPattern(string $pattern): Collection;
    
    public function getRecurrencesByDateRange(string $startDate, string $endDate): Collection;
}
