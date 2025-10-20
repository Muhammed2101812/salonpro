<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\UserPreference;
use App\Repositories\Contracts\UserPreferenceRepositoryInterface;
use App\Services\UserPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPreferenceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserPreferenceService $service;
    protected UserPreferenceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserPreferenceRepositoryInterface::class);
        $this->service = new UserPreferenceService($this->repository);
    }

    public function test_can_get_all_userPreferences(): void
    {
        UserPreference::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_userPreferences(): void
    {
        UserPreference::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_userPreference(): void
    {
        $data = UserPreference::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(UserPreference::class, $result);
        $this->assertDatabaseHas('userPreferences', ['id' => $result->id]);
    }

    public function test_can_update_userPreference(): void
    {
        $userPreference = UserPreference::factory()->create();
        $updateData = UserPreference::factory()->make()->toArray();

        $result = $this->service->update($userPreference->id, $updateData);

        $this->assertInstanceOf(UserPreference::class, $result);
    }

    public function test_can_delete_userPreference(): void
    {
        $userPreference = UserPreference::factory()->create();

        $result = $this->service->delete($userPreference->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('userPreferences', ['id' => $userPreference->id]);
    }

    public function test_can_find_userPreference_by_id(): void
    {
        $userPreference = UserPreference::factory()->create();

        $result = $this->service->findById($userPreference->id);

        $this->assertInstanceOf(UserPreference::class, $result);
        $this->assertEquals($userPreference->id, $result->id);
    }
}
