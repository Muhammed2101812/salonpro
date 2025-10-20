<?php

namespace App\Repositories\Contracts;

use App\Models\AppointmentConflict;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentConflictRepositoryInterface
{
    public function find(int $id): ?AppointmentConflict;
    
    public function all(): Collection;
    
    public function create(array $data): AppointmentConflict;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getConflictsByAppointment(int $appointmentId): Collection;
    
    public function getUnresolvedConflicts(): Collection;
    
    public function getResolvedConflicts(): Collection;
    
    public function getConflictsByType(string $type): Collection;
}
