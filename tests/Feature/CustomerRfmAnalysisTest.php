<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CustomerRfmAnalysis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRfmAnalysisTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_customerRfmAnalysiss(): void
    {
        CustomerRfmAnalysis::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/customer-rfm-analyses');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_customerRfmAnalysis(): void
    {
        $data = CustomerRfmAnalysis::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/customer-rfm-analyses', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('customer-rfm-analyses', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_customerRfmAnalysis(): void
    {
        $customerRfmAnalysis = CustomerRfmAnalysis::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/customer-rfm-analyses/{$customerRfmAnalysis->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_customerRfmAnalysis(): void
    {
        $customerRfmAnalysis = CustomerRfmAnalysis::factory()->create();
        $updateData = CustomerRfmAnalysis::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/customer-rfm-analyses/{$customerRfmAnalysis->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_customerRfmAnalysis(): void
    {
        $customerRfmAnalysis = CustomerRfmAnalysis::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/customer-rfm-analyses/{$customerRfmAnalysis->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('customer-rfm-analyses', [
            'id' => $customerRfmAnalysis->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/customer-rfm-analyses');

        $response->assertUnauthorized();
    }
}
