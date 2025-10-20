<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuditLogService $service;
    protected AuditLogRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AuditLogRepositoryInterface::class);
        $this->service = new AuditLogService($this->repository);
    }

    public function test_can_get_all_auditLogs(): void
    {
        AuditLog::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_auditLogs(): void
    {
        AuditLog::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_auditLog(): void
    {
        $data = AuditLog::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AuditLog::class, $result);
        $this->assertDatabaseHas('auditLogs', ['id' => $result->id]);
    }

    public function test_can_update_auditLog(): void
    {
        $auditLog = AuditLog::factory()->create();
        $updateData = AuditLog::factory()->make()->toArray();

        $result = $this->service->update($auditLog->id, $updateData);

        $this->assertInstanceOf(AuditLog::class, $result);
    }

    public function test_can_delete_auditLog(): void
    {
        $auditLog = AuditLog::factory()->create();

        $result = $this->service->delete($auditLog->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('auditLogs', ['id' => $auditLog->id]);
    }

    public function test_can_find_auditLog_by_id(): void
    {
        $auditLog = AuditLog::factory()->create();

        $result = $this->service->findById($auditLog->id);

        $this->assertInstanceOf(AuditLog::class, $result);
        $this->assertEquals($auditLog->id, $result->id);
    }
}
