<?php

namespace App\Repositories\Eloquent;

use App\Models\NotificationLog;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationLogRepository implements NotificationLogRepositoryInterface
{
    public function __construct(protected NotificationLog $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->with(['branch', 'queue', 'recipient'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['branch', 'queue', 'recipient'])->paginate($perPage);
    }

    public function find(int $id): ?NotificationLog
    {
        return $this->model->with(['branch', 'queue', 'recipient'])->find($id);
    }

    public function create(array $data): NotificationLog
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): NotificationLog
    {
        $log = $this->find($id);
        $log->update($data);
        return $log->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getByChannel(string $channel): Collection
    {
        return $this->model->where('channel', $channel)->get();
    }

    public function getByRecipient(string $type, int $id): Collection
    {
        return $this->model->where('recipient_type', $type)
            ->where('recipient_id', $id)
            ->get();
    }

    public function getByDateRange(string $start, string $end): Collection
    {
        return $this->model->whereBetween('created_at', [$start, $end])->get();
    }
}
