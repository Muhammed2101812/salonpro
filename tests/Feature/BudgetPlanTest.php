<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BudgetPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetPlanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_budgetPlans(): void
    {
        BudgetPlan::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/budget-plans');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_budgetPlan(): void
    {
        $data = BudgetPlan::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/budget-plans', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('budget-plans', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_budgetPlan(): void
    {
        $budgetPlan = BudgetPlan::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/budget-plans/{$budgetPlan->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_budgetPlan(): void
    {
        $budgetPlan = BudgetPlan::factory()->create();
        $updateData = BudgetPlan::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/budget-plans/{$budgetPlan->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_budgetPlan(): void
    {
        $budgetPlan = BudgetPlan::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/budget-plans/{$budgetPlan->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('budget-plans', [
            'id' => $budgetPlan->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/budget-plans');

        $response->assertUnauthorized();
    }
}
