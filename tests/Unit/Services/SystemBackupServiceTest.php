<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\SystemBackup;
use App\Repositories\Contracts\SystemBackupRepositoryInterface;
use App\Services\SystemBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemBackupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SystemBackupService $service;
    protected SystemBackupRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SystemBackupRepositoryInterface::class);
        $this->service = new SystemBackupService($this->repository);
    }

    public function test_can_get_all_systemBackups(): void
    {
        SystemBackup::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_systemBackups(): void
    {
        SystemBackup::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_systemBackup(): void
    {
        $data = SystemBackup::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(SystemBackup::class, $result);
        $this->assertDatabaseHas('systemBackups', ['id' => $result->id]);
    }

    public function test_can_update_systemBackup(): void
    {
        $systemBackup = SystemBackup::factory()->create();
        $updateData = SystemBackup::factory()->make()->toArray();

        $result = $this->service->update($systemBackup->id, $updateData);

        $this->assertInstanceOf(SystemBackup::class, $result);
    }

    public function test_can_delete_systemBackup(): void
    {
        $systemBackup = SystemBackup::factory()->create();

        $result = $this->service->delete($systemBackup->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('systemBackups', ['id' => $systemBackup->id]);
    }

    public function test_can_find_systemBackup_by_id(): void
    {
        $systemBackup = SystemBackup::factory()->create();

        $result = $this->service->findById($systemBackup->id);

        $this->assertInstanceOf(SystemBackup::class, $result);
        $this->assertEquals($systemBackup->id, $result->id);
    }
}
