<?php

namespace App\Repositories\Contracts;

use App\Models\AppointmentReminder;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentReminderRepositoryInterface
{
    public function find(int $id): ?AppointmentReminder;
    
    public function all(): Collection;
    
    public function create(array $data): AppointmentReminder;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getRemindersByAppointment(int $appointmentId): Collection;
    
    public function getPendingReminders(): Collection;
    
    public function getSentReminders(): Collection;
    
    public function getRemindersByType(string $type): Collection;
    
    public function getRemindersDueForSending(): Collection;
}
