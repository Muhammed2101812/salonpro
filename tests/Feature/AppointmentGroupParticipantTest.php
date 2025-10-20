<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AppointmentGroupParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentGroupParticipantTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_appointmentGroupParticipants(): void
    {
        AppointmentGroupParticipant::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/appointment-group-participants');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_appointmentGroupParticipant(): void
    {
        $data = AppointmentGroupParticipant::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/appointment-group-participants', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('appointment-group-participants', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_appointmentGroupParticipant(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/appointment-group-participants/{$appointmentGroupParticipant->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_appointmentGroupParticipant(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();
        $updateData = AppointmentGroupParticipant::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/appointment-group-participants/{$appointmentGroupParticipant->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_appointmentGroupParticipant(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/appointment-group-participants/{$appointmentGroupParticipant->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('appointment-group-participants', [
            'id' => $appointmentGroupParticipant->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/appointment-group-participants');

        $response->assertUnauthorized();
    }
}
