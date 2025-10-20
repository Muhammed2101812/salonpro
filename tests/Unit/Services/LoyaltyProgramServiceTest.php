<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\LoyaltyProgram;
use App\Repositories\Contracts\LoyaltyProgramRepositoryInterface;
use App\Services\LoyaltyProgramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyProgramServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoyaltyProgramService $service;
    protected LoyaltyProgramRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(LoyaltyProgramRepositoryInterface::class);
        $this->service = new LoyaltyProgramService($this->repository);
    }

    public function test_can_get_all_loyaltyPrograms(): void
    {
        LoyaltyProgram::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_loyaltyPrograms(): void
    {
        LoyaltyProgram::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_loyaltyProgram(): void
    {
        $data = LoyaltyProgram::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(LoyaltyProgram::class, $result);
        $this->assertDatabaseHas('loyaltyPrograms', ['id' => $result->id]);
    }

    public function test_can_update_loyaltyProgram(): void
    {
        $loyaltyProgram = LoyaltyProgram::factory()->create();
        $updateData = LoyaltyProgram::factory()->make()->toArray();

        $result = $this->service->update($loyaltyProgram->id, $updateData);

        $this->assertInstanceOf(LoyaltyProgram::class, $result);
    }

    public function test_can_delete_loyaltyProgram(): void
    {
        $loyaltyProgram = LoyaltyProgram::factory()->create();

        $result = $this->service->delete($loyaltyProgram->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('loyaltyPrograms', ['id' => $loyaltyProgram->id]);
    }

    public function test_can_find_loyaltyProgram_by_id(): void
    {
        $loyaltyProgram = LoyaltyProgram::factory()->create();

        $result = $this->service->findById($loyaltyProgram->id);

        $this->assertInstanceOf(LoyaltyProgram::class, $result);
        $this->assertEquals($loyaltyProgram->id, $result->id);
    }
}
