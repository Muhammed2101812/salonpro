<?php

namespace App\Repositories\Eloquent;

use App\Models\SmsProvider;
use App\Repositories\Contracts\SmsProviderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SmsProviderRepository implements SmsProviderRepositoryInterface
{
    public function __construct(protected SmsProvider $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->with(['branch'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['branch'])->paginate($perPage);
    }

    public function find(int $id): ?SmsProvider
    {
        return $this->model->with(['branch'])->find($id);
    }

    public function create(array $data): SmsProvider
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): SmsProvider
    {
        $provider = $this->find($id);
        $provider->update($data);
        return $provider->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getDefault(): ?SmsProvider
    {
        return $this->model->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    public function setDefault(int $id): SmsProvider
    {
        // Remove default from all providers
        $this->model->where('is_default', true)->update(['is_default' => false]);
        
        // Set new default
        $provider = $this->find($id);
        $provider->update(['is_default' => true, 'is_active' => true]);
        return $provider->fresh();
    }

    public function getByPriority(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();
    }
}
