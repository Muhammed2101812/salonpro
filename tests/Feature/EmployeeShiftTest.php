<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EmployeeShift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeShiftTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_employeeShifts(): void
    {
        EmployeeShift::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/employee-shifts');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_employeeShift(): void
    {
        $data = EmployeeShift::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/employee-shifts', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('employee-shifts', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_employeeShift(): void
    {
        $employeeShift = EmployeeShift::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/employee-shifts/{$employeeShift->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_employeeShift(): void
    {
        $employeeShift = EmployeeShift::factory()->create();
        $updateData = EmployeeShift::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/employee-shifts/{$employeeShift->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_employeeShift(): void
    {
        $employeeShift = EmployeeShift::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/employee-shifts/{$employeeShift->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('employee-shifts', [
            'id' => $employeeShift->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/employee-shifts');

        $response->assertUnauthorized();
    }
}
