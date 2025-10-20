<?php

namespace App\Repositories\Contracts;

use App\Models\AppointmentCancellationReason;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentCancellationReasonRepositoryInterface
{
    public function find(int $id): ?AppointmentCancellationReason;
    
    public function all(): Collection;
    
    public function create(array $data): AppointmentCancellationReason;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getActiveReasons(): Collection;
    
    public function getReasonsByBranch(int $branchId): Collection;
    
    public function getMostUsedReasons(int $limit = 10): Collection;
}
