<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\TwoFactorAuthentication;
use App\Repositories\Contracts\TwoFactorAuthenticationRepositoryInterface;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorAuthenticationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TwoFactorAuthenticationService $service;
    protected TwoFactorAuthenticationRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(TwoFactorAuthenticationRepositoryInterface::class);
        $this->service = new TwoFactorAuthenticationService($this->repository);
    }

    public function test_can_get_all_twoFactorAuthentications(): void
    {
        TwoFactorAuthentication::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_twoFactorAuthentications(): void
    {
        TwoFactorAuthentication::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_twoFactorAuthentication(): void
    {
        $data = TwoFactorAuthentication::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(TwoFactorAuthentication::class, $result);
        $this->assertDatabaseHas('twoFactorAuthentications', ['id' => $result->id]);
    }

    public function test_can_update_twoFactorAuthentication(): void
    {
        $twoFactorAuthentication = TwoFactorAuthentication::factory()->create();
        $updateData = TwoFactorAuthentication::factory()->make()->toArray();

        $result = $this->service->update($twoFactorAuthentication->id, $updateData);

        $this->assertInstanceOf(TwoFactorAuthentication::class, $result);
    }

    public function test_can_delete_twoFactorAuthentication(): void
    {
        $twoFactorAuthentication = TwoFactorAuthentication::factory()->create();

        $result = $this->service->delete($twoFactorAuthentication->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('twoFactorAuthentications', ['id' => $twoFactorAuthentication->id]);
    }

    public function test_can_find_twoFactorAuthentication_by_id(): void
    {
        $twoFactorAuthentication = TwoFactorAuthentication::factory()->create();

        $result = $this->service->findById($twoFactorAuthentication->id);

        $this->assertInstanceOf(TwoFactorAuthentication::class, $result);
        $this->assertEquals($twoFactorAuthentication->id, $result->id);
    }
}
