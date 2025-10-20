<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EmployeeAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_employeeAttendances(): void
    {
        EmployeeAttendance::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/employee-attendances');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_employeeAttendance(): void
    {
        $data = EmployeeAttendance::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/employee-attendances', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('employee-attendances', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_employeeAttendance(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/employee-attendances/{$employeeAttendance->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_employeeAttendance(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();
        $updateData = EmployeeAttendance::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/employee-attendances/{$employeeAttendance->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_employeeAttendance(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/employee-attendances/{$employeeAttendance->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('employee-attendances', [
            'id' => $employeeAttendance->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/employee-attendances');

        $response->assertUnauthorized();
    }
}
