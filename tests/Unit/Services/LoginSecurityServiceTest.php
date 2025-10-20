<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\LoginSecurity;
use App\Repositories\Contracts\LoginSecurityRepositoryInterface;
use App\Services\LoginSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginSecurityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoginSecurityService $service;
    protected LoginSecurityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(LoginSecurityRepositoryInterface::class);
        $this->service = new LoginSecurityService($this->repository);
    }

    public function test_can_get_all_loginSecuritys(): void
    {
        LoginSecurity::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_loginSecuritys(): void
    {
        LoginSecurity::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_loginSecurity(): void
    {
        $data = LoginSecurity::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(LoginSecurity::class, $result);
        $this->assertDatabaseHas('loginSecuritys', ['id' => $result->id]);
    }

    public function test_can_update_loginSecurity(): void
    {
        $loginSecurity = LoginSecurity::factory()->create();
        $updateData = LoginSecurity::factory()->make()->toArray();

        $result = $this->service->update($loginSecurity->id, $updateData);

        $this->assertInstanceOf(LoginSecurity::class, $result);
    }

    public function test_can_delete_loginSecurity(): void
    {
        $loginSecurity = LoginSecurity::factory()->create();

        $result = $this->service->delete($loginSecurity->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('loginSecuritys', ['id' => $loginSecurity->id]);
    }

    public function test_can_find_loginSecurity_by_id(): void
    {
        $loginSecurity = LoginSecurity::factory()->create();

        $result = $this->service->findById($loginSecurity->id);

        $this->assertInstanceOf(LoginSecurity::class, $result);
        $this->assertEquals($loginSecurity->id, $result->id);
    }
}
