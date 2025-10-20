<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmailProvider;
use App\Repositories\Eloquent\EmailProviderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailProviderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmailProviderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmailProviderRepository(new EmailProvider());
    }

    public function test_can_get_all_records(): void
    {
        EmailProvider::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmailProvider::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmailProvider::class, $result);
        $this->assertDatabaseHas('emailProviders', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $emailProvider = EmailProvider::factory()->create();

        $result = $this->repository->find($emailProvider->id);

        $this->assertInstanceOf(EmailProvider::class, $result);
        $this->assertEquals($emailProvider->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $emailProvider = EmailProvider::factory()->create();
        $updateData = EmailProvider::factory()->make()->toArray();

        $result = $this->repository->update($emailProvider->id, $updateData);

        $this->assertInstanceOf(EmailProvider::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $emailProvider = EmailProvider::factory()->create();

        $result = $this->repository->delete($emailProvider->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('emailProviders', ['id' => $emailProvider->id]);
    }
}
