<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Integration;
use App\Repositories\Contracts\IntegrationRepositoryInterface;
use Illuminate\Support\Collection;

class IntegrationRepository extends BaseRepository implements IntegrationRepositoryInterface
{
    public function __construct(Integration $model)
    {
        parent::__construct($model);
    }

    public function getActive(?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'configurator'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getByType(string $type, ?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'configurator'])
            ->where('integration_type', $type)
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getByProvider(string $provider, ?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'configurator'])
            ->where('provider', $provider)
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getByBranch(string $branchId): Collection
    {
        return $this->model->with(['branch', 'configurator'])
            ->where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateLastSynced(string $id): mixed
    {
        return $this->update($id, [
            'last_synced_at' => now(),
        ]);
    }

    public function updateStatus(string $id, string $status, ?string $errorMessage = null): mixed
    {
        $data = ['status' => $status];
        
        if ($errorMessage) {
            $data['error_message'] = $errorMessage;
        }

        return $this->update($id, $data);
    }
}
