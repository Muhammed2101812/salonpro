<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebhookLogResource;
use App\Services\Contracts\WebhookLogServiceInterface;
use App\Models\WebhookLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WebhookLogController extends Controller
{
    public function __construct(
        protected WebhookLogServiceInterface $webhookLogService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', WebhookLog::class);

        $webhookId = $request->query('webhook_id');
        $perPage = (int) $request->query('per_page', 15);

        if ($webhookId) {
            $logs = $this->webhookLogService->getByWebhook($webhookId, $perPage);
        } else {
            $logs = $this->webhookLogService->getAll($perPage);
        }

        return WebhookLogResource::collection($logs);
    }

    public function show(string $id): JsonResponse
    {
        $log = $this->webhookLogService->findById($id);

        return response()->json([
            'data' => WebhookLogResource::make($log),
        ]);
    }

    public function failed(): AnonymousResourceCollection
    {
        $logs = $this->webhookLogService->getFailed();

        return WebhookLogResource::collection($logs);
    }

    public function pendingRetries(): AnonymousResourceCollection
    {
        $logs = $this->webhookLogService->getPendingRetries();

        return WebhookLogResource::collection($logs);
    }

    public function retry(string $id): JsonResponse
    {
        try {
            $log = $this->webhookLogService->retry($id);

            return response()->json([
                'message' => 'Webhook retry completed',
                'data' => WebhookLogResource::make($log),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Webhook retry failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
