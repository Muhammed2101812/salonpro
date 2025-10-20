<?php

namespace App\Repositories\Contracts;

use App\Models\AppointmentCancellation;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentCancellationRepositoryInterface
{
    public function find(int $id): ?AppointmentCancellation;
    
    public function all(): Collection;
    
    public function create(array $data): AppointmentCancellation;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getCancellationByAppointment(int $appointmentId): ?AppointmentCancellation;
    
    public function getCancellationsByReason(int $reasonId): Collection;
    
    public function getCancellationsByCustomer(int $customerId): Collection;
    
    public function getCancellationsByDateRange(string $startDate, string $endDate): Collection;
    
    public function getRefundedCancellations(): Collection;
}
