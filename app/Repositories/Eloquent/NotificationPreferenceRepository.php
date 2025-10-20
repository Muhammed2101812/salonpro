<?php

namespace App\Repositories\Eloquent;

use App\Models\NotificationPreference;
use App\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationPreferenceRepository implements NotificationPreferenceRepositoryInterface
{
    public function __construct(protected NotificationPreference $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->with(['branch', 'user'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['branch', 'user'])->paginate($perPage);
    }

    public function find(int $id): ?NotificationPreference
    {
        return $this->model->with(['branch', 'user'])->find($id);
    }

    public function create(array $data): NotificationPreference
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): NotificationPreference
    {
        $preference = $this->find($id);
        $preference->update($data);
        return $preference->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getByUser(string $type, int $id): Collection
    {
        return $this->model->where('user_type', $type)
            ->where('user_id', $id)
            ->get();
    }

    public function getByNotificationType(string $type): Collection
    {
        return $this->model->where('notification_type', $type)->get();
    }

    public function getByChannel(string $channel): Collection
    {
        return $this->model->where('channel', $channel)->get();
    }

    public function getUserPreference(string $userType, int $userId, string $notificationType, string $channel): ?NotificationPreference
    {
        return $this->model->where('user_type', $userType)
            ->where('user_id', $userId)
            ->where('notification_type', $notificationType)
            ->where('channel', $channel)
            ->first();
    }
}
