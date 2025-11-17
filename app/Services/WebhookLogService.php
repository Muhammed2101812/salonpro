<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\WebhookLogRepositoryInterface;
use App\Services\Contracts\WebhookLogServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WebhookLogService extends BaseService implements WebhookLogServiceInterface
{
    public function __construct(
        protected WebhookLogRepositoryInterface $webhookLogRepository
    ) {
        parent::__construct($webhookLogRepository);
    }

    public function getByWebhook(string $webhookId, int $perPage = 15): mixed
    {
        return $this->webhookLogRepository->getByWebhook($webhookId, $perPage);
    }

    public function getFailed(): mixed
    {
        return $this->webhookLogRepository->getFailed();
    }

    public function getPendingRetries(): mixed
    {
        return $this->webhookLogRepository->getPendingRetries();
    }

    public function retry(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $log = $this->webhookLogRepository->findOrFail($id);
            $webhook = $log->webhook;

            if ($log->status === 'success') {
                throw new \RuntimeException('Cannot retry successful webhook');
            }

            if ($log->attempt >= $webhook->max_retries) {
                throw new \RuntimeException('Maximum retry attempts reached');
            }

            // Update attempt count
            $newAttempt = $log->attempt + 1;
            
            try {
                $startTime = microtime(true);
                
                $response = Http::timeout($webhook->timeout ?? 30)
                    ->withHeaders($webhook->headers ?? [])
                    ->post($webhook->url, [
                        'event' => $log->event,
                        'payload' => $log->payload,
                        'timestamp' => now()->toIso8601String(),
                        'webhook_id' => $webhook->id,
                        'retry_attempt' => $newAttempt,
                    ]);

                $duration = (microtime(true) - $startTime) * 1000;

                return $this->webhookLogRepository->update($log->id, [
                    'attempt' => $newAttempt,
                    'http_status' => $response->status(),
                    'response_body' => $response->body(),
                    'duration_ms' => (int) $duration,
                    'status' => $response->successful() ? 'success' : 'failed',
                    'error_message' => $response->successful() ? null : 'HTTP ' . $response->status(),
                    'sent_at' => now(),
                    'next_retry_at' => $response->successful() ? null : now()->addMinutes($newAttempt * 5),
                ]);
            } catch (\Exception $e) {
                return $this->webhookLogRepository->update($log->id, [
                    'attempt' => $newAttempt,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'sent_at' => now(),
                    'next_retry_at' => now()->addMinutes($newAttempt * 5),
                ]);
            }
        });
    }
}
