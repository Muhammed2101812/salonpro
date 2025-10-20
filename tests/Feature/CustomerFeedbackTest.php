<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CustomerFeedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_customerFeedbacks(): void
    {
        CustomerFeedback::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/customer-feedbacks');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_customerFeedback(): void
    {
        $data = CustomerFeedback::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/customer-feedbacks', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('customer-feedbacks', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_customerFeedback(): void
    {
        $customerFeedback = CustomerFeedback::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/customer-feedbacks/{$customerFeedback->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_customerFeedback(): void
    {
        $customerFeedback = CustomerFeedback::factory()->create();
        $updateData = CustomerFeedback::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/customer-feedbacks/{$customerFeedback->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_customerFeedback(): void
    {
        $customerFeedback = CustomerFeedback::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/customer-feedbacks/{$customerFeedback->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('customer-feedbacks', [
            'id' => $customerFeedback->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/customer-feedbacks');

        $response->assertUnauthorized();
    }
}
