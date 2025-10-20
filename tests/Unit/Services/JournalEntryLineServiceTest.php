<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\JournalEntryLine;
use App\Repositories\Contracts\JournalEntryLineRepositoryInterface;
use App\Services\JournalEntryLineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalEntryLineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected JournalEntryLineService $service;
    protected JournalEntryLineRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(JournalEntryLineRepositoryInterface::class);
        $this->service = new JournalEntryLineService($this->repository);
    }

    public function test_can_get_all_journalEntryLines(): void
    {
        JournalEntryLine::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_journalEntryLines(): void
    {
        JournalEntryLine::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_journalEntryLine(): void
    {
        $data = JournalEntryLine::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(JournalEntryLine::class, $result);
        $this->assertDatabaseHas('journalEntryLines', ['id' => $result->id]);
    }

    public function test_can_update_journalEntryLine(): void
    {
        $journalEntryLine = JournalEntryLine::factory()->create();
        $updateData = JournalEntryLine::factory()->make()->toArray();

        $result = $this->service->update($journalEntryLine->id, $updateData);

        $this->assertInstanceOf(JournalEntryLine::class, $result);
    }

    public function test_can_delete_journalEntryLine(): void
    {
        $journalEntryLine = JournalEntryLine::factory()->create();

        $result = $this->service->delete($journalEntryLine->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('journalEntryLines', ['id' => $journalEntryLine->id]);
    }

    public function test_can_find_journalEntryLine_by_id(): void
    {
        $journalEntryLine = JournalEntryLine::factory()->create();

        $result = $this->service->findById($journalEntryLine->id);

        $this->assertInstanceOf(JournalEntryLine::class, $result);
        $this->assertEquals($journalEntryLine->id, $result->id);
    }
}
