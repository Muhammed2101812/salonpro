<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ServiceAddon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAddonTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_serviceAddons(): void
    {
        ServiceAddon::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/service-addons');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_serviceAddon(): void
    {
        $data = ServiceAddon::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/service-addons', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('service-addons', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_serviceAddon(): void
    {
        $serviceAddon = ServiceAddon::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/service-addons/{$serviceAddon->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_serviceAddon(): void
    {
        $serviceAddon = ServiceAddon::factory()->create();
        $updateData = ServiceAddon::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/service-addons/{$serviceAddon->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_serviceAddon(): void
    {
        $serviceAddon = ServiceAddon::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/service-addons/{$serviceAddon->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('service-addons', [
            'id' => $serviceAddon->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/service-addons');

        $response->assertUnauthorized();
    }
}
