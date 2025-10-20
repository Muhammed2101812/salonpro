<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EmployeeSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_employeeSchedules(): void
    {
        EmployeeSchedule::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/employee-schedules');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_employeeSchedule(): void
    {
        $data = EmployeeSchedule::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/employee-schedules', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('employee-schedules', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_employeeSchedule(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/employee-schedules/{$employeeSchedule->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_employeeSchedule(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();
        $updateData = EmployeeSchedule::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/employee-schedules/{$employeeSchedule->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_employeeSchedule(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/employee-schedules/{$employeeSchedule->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('employee-schedules', [
            'id' => $employeeSchedule->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/employee-schedules');

        $response->assertUnauthorized();
    }
}
