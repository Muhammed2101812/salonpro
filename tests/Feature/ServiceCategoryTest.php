<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ServiceCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create the permission needed
        Permission::create(['name' => 'services.manage-categories']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('services.manage-categories');
    }

    public function test_can_list_serviceCategorys(): void
    {
        ServiceCategory::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/service-categories');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_serviceCategory(): void
    {
        $data = ServiceCategory::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/service-categories', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('service_categories', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_serviceCategory(): void
    {
        $serviceCategory = ServiceCategory::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/service-categories/{$serviceCategory->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_serviceCategory(): void
    {
        $serviceCategory = ServiceCategory::factory()->create();
        $updateData = ServiceCategory::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/service-categories/{$serviceCategory->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_serviceCategory(): void
    {
        $serviceCategory = ServiceCategory::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/service-categories/{$serviceCategory->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('service_categories', [
            'id' => $serviceCategory->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/service-categories');

        $response->assertUnauthorized();
    }

    public function test_cannot_create_service_category_without_permission(): void
    {
        $unauthorizedUser = User::factory()->create();
        $data = ServiceCategory::factory()->make()->toArray();

        $response = $this->actingAs($unauthorizedUser, 'sanctum')
            ->postJson('/api/v1/service-categories', $data);

        $response->assertForbidden();
    }

    public function test_cannot_update_service_category_without_permission(): void
    {
        $unauthorizedUser = User::factory()->create();
        $serviceCategory = ServiceCategory::factory()->create();
        $updateData = ServiceCategory::factory()->make()->toArray();

        $response = $this->actingAs($unauthorizedUser, 'sanctum')
            ->putJson("/api/v1/service-categories/{$serviceCategory->id}", $updateData);

        $response->assertForbidden();
    }
}
