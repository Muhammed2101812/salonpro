<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\KpiDefinition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiDefinitionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_kpiDefinitions(): void
    {
        KpiDefinition::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/kpi-definitions');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_kpiDefinition(): void
    {
        $data = KpiDefinition::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/kpi-definitions', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('kpi-definitions', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_kpiDefinition(): void
    {
        $kpiDefinition = KpiDefinition::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/kpi-definitions/{$kpiDefinition->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_kpiDefinition(): void
    {
        $kpiDefinition = KpiDefinition::factory()->create();
        $updateData = KpiDefinition::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/kpi-definitions/{$kpiDefinition->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_kpiDefinition(): void
    {
        $kpiDefinition = KpiDefinition::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/kpi-definitions/{$kpiDefinition->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('kpi-definitions', [
            'id' => $kpiDefinition->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/kpi-definitions');

        $response->assertUnauthorized();
    }
}
