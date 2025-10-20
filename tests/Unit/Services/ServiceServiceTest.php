<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\;
use App\Repositories\Contracts\RepositoryInterface;
use App\Services\ServiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceService $service;
    protected RepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(RepositoryInterface::class);
        $this->service = new ServiceService($this->repository);
    }

    public function test_can_get_all_s(): void
    {
        ::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_s(): void
    {
        ::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_(): void
    {
        $data = ::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(::class, $result);
        $this->assertDatabaseHas('s', ['id' => $result->id]);
    }

    public function test_can_update_(): void
    {
        $ = ::factory()->create();
        $updateData = ::factory()->make()->toArray();

        $result = $this->service->update($->id, $updateData);

        $this->assertInstanceOf(::class, $result);
    }

    public function test_can_delete_(): void
    {
        $ = ::factory()->create();

        $result = $this->service->delete($->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('s', ['id' => $->id]);
    }

    public function test_can_find__by_id(): void
    {
        $ = ::factory()->create();

        $result = $this->service->findById($->id);

        $this->assertInstanceOf(::class, $result);
        $this->assertEquals($->id, $result->id);
    }
}
