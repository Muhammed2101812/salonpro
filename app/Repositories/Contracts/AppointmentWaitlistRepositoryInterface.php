<?php

namespace App\Repositories\Contracts;

use App\Models\AppointmentWaitlist;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentWaitlistRepositoryInterface
{
    public function find(int $id): ?AppointmentWaitlist;
    
    public function all(): Collection;
    
    public function create(array $data): AppointmentWaitlist;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getWaitlistByBranch(int $branchId): Collection;
    
    public function getWaitlistByCustomer(int $customerId): Collection;
    
    public function getActiveWaitlist(): Collection;
    
    public function getWaitlistByService(int $serviceId): Collection;
    
    public function getWaitlistByDateRange(string $startDate, string $endDate): Collection;
}
