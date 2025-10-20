<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EmployeeSkill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeSkillTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_employeeSkills(): void
    {
        EmployeeSkill::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/employee-skills');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_employeeSkill(): void
    {
        $data = EmployeeSkill::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/employee-skills', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('employee-skills', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_employeeSkill(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/employee-skills/{$employeeSkill->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_employeeSkill(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();
        $updateData = EmployeeSkill::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/employee-skills/{$employeeSkill->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_employeeSkill(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/employee-skills/{$employeeSkill->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('employee-skills', [
            'id' => $employeeSkill->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/employee-skills');

        $response->assertUnauthorized();
    }
}
