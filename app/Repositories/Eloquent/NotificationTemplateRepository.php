<?php

namespace App\Repositories\Eloquent;

use App\Models\NotificationTemplate;
use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationTemplateRepository implements NotificationTemplateRepositoryInterface
{
    public function __construct(protected NotificationTemplate $model)
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

    public function find(int $id): ?NotificationTemplate
    {
        return $this->model->with(['branch'])->find($id);
    }

    public function create(array $data): NotificationTemplate
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): NotificationTemplate
    {
        $template = $this->find($id);
        $template->update($data);
        return $template->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getByType(string $type): Collection
    {
        return $this->model->where('type', $type)->get();
    }

    public function getByChannel(string $channel): Collection
    {
        return $this->model->where('channel', $channel)->get();
    }

    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getByBranch(int $branchId): Collection
    {
        return $this->model->where('branch_id', $branchId)->get();
    }
}
