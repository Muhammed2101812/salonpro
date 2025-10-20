<?php

namespace App\Repositories\Contracts;

use App\Models\AppointmentGroupParticipant;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentGroupParticipantRepositoryInterface
{
    public function find(int $id): ?AppointmentGroupParticipant;
    
    public function all(): Collection;
    
    public function create(array $data): AppointmentGroupParticipant;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getParticipantsByGroup(int $groupId): Collection;
    
    public function getParticipantsByCustomer(int $customerId): Collection;
    
    public function getConfirmedParticipants(int $groupId): Collection;
    
    public function getPendingParticipants(int $groupId): Collection;
}
