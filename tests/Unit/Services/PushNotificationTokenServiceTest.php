<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\PushNotificationToken;
use App\Repositories\Contracts\PushNotificationTokenRepositoryInterface;
use App\Services\PushNotificationTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushNotificationTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PushNotificationTokenService $service;
    protected PushNotificationTokenRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PushNotificationTokenRepositoryInterface::class);
        $this->service = new PushNotificationTokenService($this->repository);
    }

    public function test_can_get_all_pushNotificationTokens(): void
    {
        PushNotificationToken::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_pushNotificationTokens(): void
    {
        PushNotificationToken::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_pushNotificationToken(): void
    {
        $data = PushNotificationToken::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(PushNotificationToken::class, $result);
        $this->assertDatabaseHas('pushNotificationTokens', ['id' => $result->id]);
    }

    public function test_can_update_pushNotificationToken(): void
    {
        $pushNotificationToken = PushNotificationToken::factory()->create();
        $updateData = PushNotificationToken::factory()->make()->toArray();

        $result = $this->service->update($pushNotificationToken->id, $updateData);

        $this->assertInstanceOf(PushNotificationToken::class, $result);
    }

    public function test_can_delete_pushNotificationToken(): void
    {
        $pushNotificationToken = PushNotificationToken::factory()->create();

        $result = $this->service->delete($pushNotificationToken->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('pushNotificationTokens', ['id' => $pushNotificationToken->id]);
    }

    public function test_can_find_pushNotificationToken_by_id(): void
    {
        $pushNotificationToken = PushNotificationToken::factory()->create();

        $result = $this->service->findById($pushNotificationToken->id);

        $this->assertInstanceOf(PushNotificationToken::class, $result);
        $this->assertEquals($pushNotificationToken->id, $result->id);
    }
}
