<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\NotificationTemplate;
use App\Repositories\Eloquent\NotificationTemplateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTemplateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationTemplateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new NotificationTemplateRepository(new NotificationTemplate());
    }

    public function test_can_get_all_records(): void
    {
        NotificationTemplate::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = NotificationTemplate::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(NotificationTemplate::class, $result);
        $this->assertDatabaseHas('notificationTemplates', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();

        $result = $this->repository->find($notificationTemplate->id);

        $this->assertInstanceOf(NotificationTemplate::class, $result);
        $this->assertEquals($notificationTemplate->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();
        $updateData = NotificationTemplate::factory()->make()->toArray();

        $result = $this->repository->update($notificationTemplate->id, $updateData);

        $this->assertInstanceOf(NotificationTemplate::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();

        $result = $this->repository->delete($notificationTemplate->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationTemplates', ['id' => $notificationTemplate->id]);
    }
}
