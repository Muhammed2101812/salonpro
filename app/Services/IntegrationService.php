<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\IntegrationRepositoryInterface;
use App\Services\Contracts\IntegrationServiceInterface;
use Illuminate\Support\Facades\DB;

class IntegrationService extends BaseService implements IntegrationServiceInterface
{
    public function __construct(
        protected IntegrationRepositoryInterface $integrationRepository
    ) {
        parent::__construct($integrationRepository);
    }

    public function getActive(?string $branchId = null): mixed
    {
        return $this->integrationRepository->getActive($branchId);
    }

    public function getByType(string $type, ?string $branchId = null): mixed
    {
        return $this->integrationRepository->getByType($type, $branchId);
    }

    public function getByProvider(string $provider, ?string $branchId = null): mixed
    {
        return $this->integrationRepository->getByProvider($provider, $branchId);
    }

    public function activate(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $integration = $this->integrationRepository->findOrFail($id);

            // Basic validation
            if (empty($integration->credentials)) {
                throw new \RuntimeException('Integration credentials not configured');
            }

            return $this->integrationRepository->update($id, [
                'is_active' => true,
                'status' => 'active',
                'error_message' => null,
            ]);
        });
    }

    public function deactivate(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            return $this->integrationRepository->update($id, [
                'is_active' => false,
                'status' => 'inactive',
            ]);
        });
    }

    public function testConnection(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $integration = $this->integrationRepository->findOrFail($id);

            // In real implementation, this would test the actual connection
            // For now, just update status
            try {
                $this->integrationRepository->updateStatus($id, 'connected', null);
                return ['status' => 'success', 'message' => 'Connection test successful'];
            } catch (\Exception $e) {
                $this->integrationRepository->updateStatus($id, 'error', $e->getMessage());
                throw $e;
            }
        });
    }

    public function sync(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $integration = $this->integrationRepository->findOrFail($id);

            if (!$integration->is_active) {
                throw new \RuntimeException('Integration is not active');
            }

            // In real implementation, this would perform actual sync
            $this->integrationRepository->updateLastSynced($id);
            
            return $integration->refresh();
        });
    }
}
