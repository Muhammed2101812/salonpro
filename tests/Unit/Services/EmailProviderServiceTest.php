<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmailProvider;
use App\Repositories\Contracts\EmailProviderRepositoryInterface;
use App\Services\EmailProviderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailProviderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmailProviderService $service;
    protected EmailProviderRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmailProviderRepositoryInterface::class);
        $this->service = new EmailProviderService($this->repository);
    }

    public function test_can_get_all_emailProviders(): void
    {
        EmailProvider::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_emailProviders(): void
    {
        EmailProvider::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_emailProvider(): void
    {
        $data = EmailProvider::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmailProvider::class, $result);
        $this->assertDatabaseHas('emailProviders', ['id' => $result->id]);
    }

    public function test_can_update_emailProvider(): void
    {
        $emailProvider = EmailProvider::factory()->create();
        $updateData = EmailProvider::factory()->make()->toArray();

        $result = $this->service->update($emailProvider->id, $updateData);

        $this->assertInstanceOf(EmailProvider::class, $result);
    }

    public function test_can_delete_emailProvider(): void
    {
        $emailProvider = EmailProvider::factory()->create();

        $result = $this->service->delete($emailProvider->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('emailProviders', ['id' => $emailProvider->id]);
    }

    public function test_can_find_emailProvider_by_id(): void
    {
        $emailProvider = EmailProvider::factory()->create();

        $result = $this->service->findById($emailProvider->id);

        $this->assertInstanceOf(EmailProvider::class, $result);
        $this->assertEquals($emailProvider->id, $result->id);
    }
}
