<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\NotificationPreference;

interface NotificationPreferenceRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?NotificationPreference;
    
    public function create(array $data): NotificationPreference;
    
    public function update(int $id, array $data): NotificationPreference;
    
    public function delete(int $id): bool;
    
    public function getByUser(string $type, int $id): Collection;
    
    public function getByNotificationType(string $type): Collection;
    
    public function getByChannel(string $channel): Collection;
    
    public function getUserPreference(string $userType, int $userId, string $notificationType, string $channel): ?NotificationPreference;
}
