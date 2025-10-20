<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\SmsProvider;
use App\Repositories\Eloquent\SmsProviderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsProviderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SmsProviderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new SmsProviderRepository(new SmsProvider());
    }

    public function test_can_get_all_records(): void
    {
        SmsProvider::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = SmsProvider::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(SmsProvider::class, $result);
        $this->assertDatabaseHas('smsProviders', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $smsProvider = SmsProvider::factory()->create();

        $result = $this->repository->find($smsProvider->id);

        $this->assertInstanceOf(SmsProvider::class, $result);
        $this->assertEquals($smsProvider->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $smsProvider = SmsProvider::factory()->create();
        $updateData = SmsProvider::factory()->make()->toArray();

        $result = $this->repository->update($smsProvider->id, $updateData);

        $this->assertInstanceOf(SmsProvider::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $smsProvider = SmsProvider::factory()->create();

        $result = $this->repository->delete($smsProvider->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('smsProviders', ['id' => $smsProvider->id]);
    }
}
