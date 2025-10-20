<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\SmsProvider;

interface SmsProviderRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?SmsProvider;
    
    public function create(array $data): SmsProvider;
    
    public function update(int $id, array $data): SmsProvider;
    
    public function delete(int $id): bool;
    
    public function getActive(): Collection;
    
    public function getDefault(): ?SmsProvider;
    
    public function setDefault(int $id): SmsProvider;
    
    public function getByPriority(): Collection;
}
