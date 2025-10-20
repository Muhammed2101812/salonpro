<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\JournalEntry;
use App\Repositories\Contracts\JournalEntryRepositoryInterface;
use App\Services\JournalEntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalEntryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected JournalEntryService $service;
    protected JournalEntryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(JournalEntryRepositoryInterface::class);
        $this->service = new JournalEntryService($this->repository);
    }

    public function test_can_get_all_journalEntrys(): void
    {
        JournalEntry::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_journalEntrys(): void
    {
        JournalEntry::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_journalEntry(): void
    {
        $data = JournalEntry::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(JournalEntry::class, $result);
        $this->assertDatabaseHas('journalEntrys', ['id' => $result->id]);
    }

    public function test_can_update_journalEntry(): void
    {
        $journalEntry = JournalEntry::factory()->create();
        $updateData = JournalEntry::factory()->make()->toArray();

        $result = $this->service->update($journalEntry->id, $updateData);

        $this->assertInstanceOf(JournalEntry::class, $result);
    }

    public function test_can_delete_journalEntry(): void
    {
        $journalEntry = JournalEntry::factory()->create();

        $result = $this->service->delete($journalEntry->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('journalEntrys', ['id' => $journalEntry->id]);
    }

    public function test_can_find_journalEntry_by_id(): void
    {
        $journalEntry = JournalEntry::factory()->create();

        $result = $this->service->findById($journalEntry->id);

        $this->assertInstanceOf(JournalEntry::class, $result);
        $this->assertEquals($journalEntry->id, $result->id);
    }
}
