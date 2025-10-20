<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AppointmentConflict;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentConflictTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_appointmentConflicts(): void
    {
        AppointmentConflict::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/appointment-conflicts');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_appointmentConflict(): void
    {
        $data = AppointmentConflict::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/appointment-conflicts', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('appointment-conflicts', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_appointmentConflict(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/appointment-conflicts/{$appointmentConflict->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_appointmentConflict(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();
        $updateData = AppointmentConflict::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/appointment-conflicts/{$appointmentConflict->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_appointmentConflict(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/appointment-conflicts/{$appointmentConflict->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('appointment-conflicts', [
            'id' => $appointmentConflict->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/appointment-conflicts');

        $response->assertUnauthorized();
    }
}
