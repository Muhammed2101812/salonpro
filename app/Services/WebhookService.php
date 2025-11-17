<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\WebhookRepositoryInterface;
use App\Repositories\Contracts\WebhookLogRepositoryInterface;
use App\Services\Contracts\WebhookServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WebhookService extends BaseService implements WebhookServiceInterface
{
    public function __construct(
        protected WebhookRepositoryInterface $webhookRepository,
        protected WebhookLogRepositoryInterface $webhookLogRepository
    ) {
        parent::__construct($webhookRepository);
    }

    public function getActive(?string $branchId = null): mixed
    {
        return $this->webhookRepository->getActive($branchId);
    }

    public function getByEvent(string $event, ?string $branchId = null): mixed
    {
        return $this->webhookRepository->getByEvent($event, $branchId);
    }

    public function trigger(string $id, string $event, array $payload): mixed
    {
        return DB::transaction(function () use ($id, $event, $payload) {
            $webhook = $this->webhookRepository->findOrFail($id);

            if (!$webhook->is_active) {
                throw new \RuntimeException('Webhook is not active');
            }

            if (!in_array($event, $webhook->events)) {
                throw new \RuntimeException("Webhook is not subscribed to event: {$event}");
            }

            // Create webhook log
            $log = $this->webhookLogRepository->create([
                'webhook_id' => $webhook->id,
                'event' => $event,
                'payload' => $payload,
                'status' => 'pending',
                'attempt' => 1,
                'sent_at' => now(),
            ]);

            // Send webhook (in real app, this would be queued)
            try {
                $startTime = microtime(true);
                
                $response = Http::timeout($webhook->timeout ?? 30)
                    ->withHeaders($webhook->headers ?? [])
                    ->post($webhook->url, [
                        'event' => $event,
                        'payload' => $payload,
                        'timestamp' => now()->toIso8601String(),
                        'webhook_id' => $webhook->id,
                    ]);

                $duration = (microtime(true) - $startTime) * 1000;

                $this->webhookLogRepository->update($log->id, [
                    'http_status' => $response->status(),
                    'response_body' => $response->body(),
                    'duration_ms' => (int) $duration,
                    'status' => $response->successful() ? 'success' : 'failed',
                    'error_message' => $response->successful() ? null : 'HTTP ' . $response->status(),
                ]);

                if ($response->successful()) {
                    $this->webhookRepository->incrementSuccessCount($webhook->id);
                } else {
                    $this->webhookRepository->incrementFailureCount($webhook->id);
                }

                $this->webhookRepository->updateLastTriggered($webhook->id);

                return $log->refresh();
            } catch (\Exception $e) {
                $this->webhookLogRepository->update($log->id, [
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $this->webhookRepository->incrementFailureCount($webhook->id);

                throw $e;
            }
        });
    }

    public function activate(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            return $this->webhookRepository->update($id, ['is_active' => true]);
        });
    }

    public function deactivate(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            return $this->webhookRepository->update($id, ['is_active' => false]);
        });
    }

    public function test(string $id): mixed
    {
        return $this->trigger($id, 'test', [
            'message' => 'This is a test webhook',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
