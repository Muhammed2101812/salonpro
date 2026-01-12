<?php

namespace Tests\Feature;

use App\Models\NotificationQueue;
use App\Models\PushNotificationToken;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = app(NotificationService::class);
    }

    public function test_send_push_notification_successfully()
    {
        // Setup FCM config
        Config::set('services.fcm.key', 'test_server_key');

        // Create user and tokens
        $user = User::factory()->create();
        $token1 = PushNotificationToken::create([
            'user_id' => $user->id,
            'token' => 'token_1',
            'provider' => 'fcm',
            'is_active' => true,
        ]);
        $token2 = PushNotificationToken::create([
            'user_id' => $user->id,
            'token' => 'token_2',
            'provider' => 'fcm',
            'is_active' => true,
        ]);

        // Create notification
        $notification = NotificationQueue::create([
            'recipient_type' => User::class,
            'recipient_id' => $user->id,
            'channel' => 'push',
            'subject' => 'Test Subject',
            'message' => 'Test Body', // Note: Model uses message, Service maps content to message? Wait, check Service.
            'status' => 'pending',
            'attempts' => 0,
            'metadata' => ['key' => 'value'],
        ]);

        // Service uses 'content' key in input, but NotificationQueue model likely stores it in 'message' or 'content'.
        // Checking NotificationQueue model: has 'message'.
        // Checking NotificationService::queueNotification: maps 'content' => $data['content'].
        // But here we are manually creating the notification record for sendNotification.
        // The Service calls $notification->content or message?
        // Let's check NotificationService again.
        // " 'body' => $notification->content ?? '', "
        // But NotificationQueue model has 'message'. Does it have 'content'?
        // Model $fillable says 'message'. It does NOT say 'content'.
        // So $notification->content might be null if accessed directly unless there is an accessor.
        // Let's verify NotificationQueue model content again.

        // Mock Http
        Http::fake([
            'https://fcm.googleapis.com/fcm/send' => Http::response([
                'success' => 2,
                'failure' => 0,
                'results' => [
                    ['message_id' => '1'],
                    ['message_id' => '2'],
                ]
            ], 200),
        ]);

        // Run
        $result = $this->notificationService->sendNotification($notification->id);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals('sent', $notification->fresh()->status);

        Http::assertSent(function ($request) {
            return $request->url() == 'https://fcm.googleapis.com/fcm/send' &&
                   $request['registration_ids'] == ['token_1', 'token_2'] &&
                   $request['notification']['title'] == 'Test Subject' &&
                   $request['notification']['body'] == 'Test Body';
        });
    }

    public function test_send_push_notification_handles_invalid_tokens()
    {
        Config::set('services.fcm.key', 'test_server_key');

        $user = User::factory()->create();
        $token = PushNotificationToken::create([
            'user_id' => $user->id,
            'token' => 'invalid_token',
            'provider' => 'fcm',
            'is_active' => true,
        ]);

        $notification = NotificationQueue::create([
            'recipient_type' => User::class,
            'recipient_id' => $user->id,
            'channel' => 'push',
            'status' => 'pending',
        ]);

        Http::fake([
            'https://fcm.googleapis.com/fcm/send' => Http::response([
                'success' => 0,
                'failure' => 1,
                'results' => [
                    ['error' => 'NotRegistered'],
                ]
            ], 200),
        ]);

        $this->notificationService->sendNotification($notification->id);

        $this->assertFalse($token->fresh()->is_active);
    }
}
