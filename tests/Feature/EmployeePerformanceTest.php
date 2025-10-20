<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EmployeePerformance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeePerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_employeePerformances(): void
    {
        EmployeePerformance::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/employee-performances');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_employeePerformance(): void
    {
        $data = EmployeePerformance::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/employee-performances', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('employee-performances', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_employeePerformance(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/employee-performances/{$employeePerformance->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_employeePerformance(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();
        $updateData = EmployeePerformance::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/employee-performances/{$employeePerformance->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_employeePerformance(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/employee-performances/{$employeePerformance->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('employee-performances', [
            'id' => $employeePerformance->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/employee-performances');

        $response->assertUnauthorized();
    }
}
