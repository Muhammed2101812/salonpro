<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Integration\StoreIntegrationRequest;
use App\Http\Requests\Integration\UpdateIntegrationRequest;
use App\Http\Resources\IntegrationResource;
use App\Services\Contracts\IntegrationServiceInterface;
use App\Models\Integration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IntegrationController extends Controller
{
    public function __construct(
        protected IntegrationServiceInterface $integrationService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Integration::class);

        $perPage = (int) $request->query('per_page', 15);
        $integrations = $this->integrationService->getAll($perPage);

        return IntegrationResource::collection($integrations);
    }

    public function store(StoreIntegrationRequest $request): JsonResponse
    {
        $this->authorize('create', Integration::class);

        $integration = $this->integrationService->create($request->validated());

        $this->authorize('view', $integration);


        return response()->json([
            'message' => 'Integration created successfully',
            'data' => IntegrationResource::make($integration),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $integration = $this->integrationService->findById($id);

        return response()->json([
            'data' => IntegrationResource::make($integration),
        ]);
    }

    public function update(UpdateIntegrationRequest $request, string $id): JsonResponse
    {
        $integration = $this->integrationService->update($id, $request->validated());

        $this->authorize('update', $integration);


        return response()->json([
            'message' => 'Integration updated successfully',
            'data' => IntegrationResource::make($integration),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $integration = $this->integrationService->findByIdOrFail($id);

        $this->authorize('delete', $integration);

        $this->integrationService->delete($id);

        return response()->json([
            'message' => 'Integration deleted successfully',
        ]);
    }

    public function active(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->query('branch_id');
        $integrations = $this->integrationService->getActive($branchId);

        return IntegrationResource::collection($integrations);
    }

    public function activate(string $id): JsonResponse
    {
        try {
            $integration = $this->integrationService->activate($id);

            return response()->json([
                'message' => 'Integration activated successfully',
                'data' => IntegrationResource::make($integration),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Integration activation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deactivate(string $id): JsonResponse
    {
        $integration = $this->integrationService->deactivate($id);

        return response()->json([
            'message' => 'Integration deactivated successfully',
            'data' => IntegrationResource::make($integration),
        ]);
    }

    public function testConnection(string $id): JsonResponse
    {
        try {
            $result = $this->integrationService->testConnection($id);

            return response()->json([
                'message' => 'Connection test completed',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Connection test failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function sync(string $id): JsonResponse
    {
        try {
            $integration = $this->integrationService->sync($id);

            return response()->json([
                'message' => 'Integration synced successfully',
                'data' => IntegrationResource::make($integration),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Integration sync failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
