<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreWebhookRequest;
use App\Http\Requests\UpdateWebhookRequest;
use App\Http\Resources\WebhookResource;
use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WebhookController extends BaseController
{
    public function __construct(
        protected WebhookService $webhookService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $webhooks = $this->webhookService->getPaginated($perPage);

            return $this->sendPaginated(
                WebhookResource::collection($webhooks),
                'Webhooks başarıyla getirildi'
            );
        }

        $webhooks = $this->webhookService->getAll();

        return WebhookResource::collection($webhooks);
    }

    public function store(StoreWebhookRequest $request): JsonResponse
    {
        $webhook = $this->webhookService->create($request->validated());

        return $this->sendSuccess(
            new WebhookResource($webhook),
            'Webhook başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $webhook = $this->webhookService->findByIdOrFail($id);

        return $this->sendSuccess(
            new WebhookResource($webhook),
            'Webhook başarıyla getirildi'
        );
    }

    public function update(UpdateWebhookRequest $request, string $id): JsonResponse
    {
        $webhook = $this->webhookService->update($id, $request->validated());

        return $this->sendSuccess(
            new WebhookResource($webhook),
            'Webhook başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->webhookService->delete($id);

        return $this->sendSuccess(
            null,
            'Webhook başarıyla silindi'
        );
    }
}
