<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\EmailProvider;

interface EmailProviderRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?EmailProvider;
    
    public function create(array $data): EmailProvider;
    
    public function update(int $id, array $data): EmailProvider;
    
    public function delete(int $id): bool;
    
    public function getActive(): Collection;
    
    public function getDefault(): ?EmailProvider;
    
    public function setDefault(int $id): EmailProvider;
    
    public function getByPriority(): Collection;
}
