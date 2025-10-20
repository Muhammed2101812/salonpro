<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AppointmentCancellationReason;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentCancellationReasonTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_appointmentCancellationReasons(): void
    {
        AppointmentCancellationReason::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/appointment-cancellation-reasons');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_appointmentCancellationReason(): void
    {
        $data = AppointmentCancellationReason::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/appointment-cancellation-reasons', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('appointment-cancellation-reasons', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_appointmentCancellationReason(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/appointment-cancellation-reasons/{$appointmentCancellationReason->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_appointmentCancellationReason(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();
        $updateData = AppointmentCancellationReason::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/appointment-cancellation-reasons/{$appointmentCancellationReason->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_appointmentCancellationReason(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/appointment-cancellation-reasons/{$appointmentCancellationReason->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('appointment-cancellation-reasons', [
            'id' => $appointmentCancellationReason->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/appointment-cancellation-reasons');

        $response->assertUnauthorized();
    }
}
