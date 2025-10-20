<?php

namespace App\Repositories\Contracts;

use App\Models\AppointmentGroup;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentGroupRepositoryInterface
{
    public function find(int $id): ?AppointmentGroup;
    
    public function all(): Collection;
    
    public function create(array $data): AppointmentGroup;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getGroupsByBranch(int $branchId): Collection;
    
    public function getGroupsByService(int $serviceId): Collection;
    
    public function getUpcomingGroups(): Collection;
    
    public function getGroupsWithAvailableSpots(): Collection;
}
