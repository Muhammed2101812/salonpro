<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Webhook\StoreWebhookRequest;
use App\Http\Requests\Webhook\UpdateWebhookRequest;
use App\Http\Resources\WebhookResource;
use App\Services\Contracts\WebhookServiceInterface;
use App\Models\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WebhookController extends Controller
{
    public function __construct(
        protected WebhookServiceInterface $webhookService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Webhook::class);

        $perPage = (int) $request->query('per_page', 15);
        $webhooks = $this->webhookService->getAll($perPage);

        return WebhookResource::collection($webhooks);
    }

    public function store(StoreWebhookRequest $request): JsonResponse
    {
        $this->authorize('create', Webhook::class);

        $webhook = $this->webhookService->create($request->validated());

        $this->authorize('view', $webhook);


        return response()->json([
            'message' => 'Webhook created successfully',
            'data' => WebhookResource::make($webhook),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $webhook = $this->webhookService->findById($id);

        return response()->json([
            'data' => WebhookResource::make($webhook),
        ]);
    }

    public function update(UpdateWebhookRequest $request, string $id): JsonResponse
    {
        $webhook = $this->webhookService->update($id, $request->validated());

        $this->authorize('update', $webhook);


        return response()->json([
            'message' => 'Webhook updated successfully',
            'data' => WebhookResource::make($webhook),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $webhook = $this->webhookService->findByIdOrFail($id);

        $this->authorize('delete', $webhook);

        $this->webhookService->delete($id);

        return response()->json([
            'message' => 'Webhook deleted successfully',
        ]);
    }

    public function active(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->query('branch_id');
        $webhooks = $this->webhookService->getActive($branchId);

        return WebhookResource::collection($webhooks);
    }

    public function activate(string $id): JsonResponse
    {
        $webhook = $this->webhookService->activate($id);

        return response()->json([
            'message' => 'Webhook activated successfully',
            'data' => WebhookResource::make($webhook),
        ]);
    }

    public function deactivate(string $id): JsonResponse
    {
        $webhook = $this->webhookService->deactivate($id);

        return response()->json([
            'message' => 'Webhook deactivated successfully',
            'data' => WebhookResource::make($webhook),
        ]);
    }

    public function test(string $id): JsonResponse
    {
        try {
            $result = $this->webhookService->test($id);

            return response()->json([
                'message' => 'Webhook test completed',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Webhook test failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function trigger(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'event' => ['required', 'string'],
            'payload' => ['required', 'array'],
        ]);

        try {
            $result = $this->webhookService->trigger(
                $id,
                $request->input('event'),
                $request->input('payload')
            );

            return response()->json([
                'message' => 'Webhook triggered successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Webhook trigger failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
