<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AppointmentRecurrence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRecurrenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_appointmentRecurrences(): void
    {
        AppointmentRecurrence::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/appointment-recurrences');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_appointmentRecurrence(): void
    {
        $data = AppointmentRecurrence::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/appointment-recurrences', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('appointment-recurrences', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_appointmentRecurrence(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/appointment-recurrences/{$appointmentRecurrence->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_appointmentRecurrence(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();
        $updateData = AppointmentRecurrence::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/appointment-recurrences/{$appointmentRecurrence->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_appointmentRecurrence(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/appointment-recurrences/{$appointmentRecurrence->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('appointment-recurrences', [
            'id' => $appointmentRecurrence->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/appointment-recurrences');

        $response->assertUnauthorized();
    }
}
