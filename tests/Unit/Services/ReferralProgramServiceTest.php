<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ReferralProgram;
use App\Repositories\Contracts\ReferralProgramRepositoryInterface;
use App\Services\ReferralProgramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralProgramServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReferralProgramService $service;
    protected ReferralProgramRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ReferralProgramRepositoryInterface::class);
        $this->service = new ReferralProgramService($this->repository);
    }

    public function test_can_get_all_referralPrograms(): void
    {
        ReferralProgram::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_referralPrograms(): void
    {
        ReferralProgram::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_referralProgram(): void
    {
        $data = ReferralProgram::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ReferralProgram::class, $result);
        $this->assertDatabaseHas('referralPrograms', ['id' => $result->id]);
    }

    public function test_can_update_referralProgram(): void
    {
        $referralProgram = ReferralProgram::factory()->create();
        $updateData = ReferralProgram::factory()->make()->toArray();

        $result = $this->service->update($referralProgram->id, $updateData);

        $this->assertInstanceOf(ReferralProgram::class, $result);
    }

    public function test_can_delete_referralProgram(): void
    {
        $referralProgram = ReferralProgram::factory()->create();

        $result = $this->service->delete($referralProgram->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('referralPrograms', ['id' => $referralProgram->id]);
    }

    public function test_can_find_referralProgram_by_id(): void
    {
        $referralProgram = ReferralProgram::factory()->create();

        $result = $this->service->findById($referralProgram->id);

        $this->assertInstanceOf(ReferralProgram::class, $result);
        $this->assertEquals($referralProgram->id, $result->id);
    }
}
