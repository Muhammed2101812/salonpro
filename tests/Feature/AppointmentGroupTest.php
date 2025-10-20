<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AppointmentGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentGroupTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_appointmentGroups(): void
    {
        AppointmentGroup::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/appointment-groups');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_appointmentGroup(): void
    {
        $data = AppointmentGroup::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/appointment-groups', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('appointment-groups', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_appointmentGroup(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/appointment-groups/{$appointmentGroup->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_appointmentGroup(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();
        $updateData = AppointmentGroup::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/appointment-groups/{$appointmentGroup->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_appointmentGroup(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/appointment-groups/{$appointmentGroup->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('appointment-groups', [
            'id' => $appointmentGroup->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/appointment-groups');

        $response->assertUnauthorized();
    }
}
