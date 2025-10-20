<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AppointmentCancellation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_appointmentCancellations(): void
    {
        AppointmentCancellation::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/appointment-cancellations');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_appointmentCancellation(): void
    {
        $data = AppointmentCancellation::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/appointment-cancellations', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('appointment-cancellations', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_appointmentCancellation(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/appointment-cancellations/{$appointmentCancellation->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_appointmentCancellation(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();
        $updateData = AppointmentCancellation::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/appointment-cancellations/{$appointmentCancellation->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_appointmentCancellation(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/appointment-cancellations/{$appointmentCancellation->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('appointment-cancellations', [
            'id' => $appointmentCancellation->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/appointment-cancellations');

        $response->assertUnauthorized();
    }
}
