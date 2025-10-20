<?php

namespace App\Repositories\Eloquent;

use App\Models\EmailProvider;
use App\Repositories\Contracts\EmailProviderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmailProviderRepository implements EmailProviderRepositoryInterface
{
    public function __construct(protected EmailProvider $model)
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

    public function find(int $id): ?EmailProvider
    {
        return $this->model->with(['branch'])->find($id);
    }

    public function create(array $data): EmailProvider
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): EmailProvider
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

    public function getDefault(): ?EmailProvider
    {
        return $this->model->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    public function setDefault(int $id): EmailProvider
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
