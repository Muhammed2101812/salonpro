<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ServicePricingRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePricingRuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_servicePricingRules(): void
    {
        ServicePricingRule::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/service-pricing-rules');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_servicePricingRule(): void
    {
        $data = ServicePricingRule::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/service-pricing-rules', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('service-pricing-rules', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_servicePricingRule(): void
    {
        $servicePricingRule = ServicePricingRule::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/service-pricing-rules/{$servicePricingRule->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_servicePricingRule(): void
    {
        $servicePricingRule = ServicePricingRule::factory()->create();
        $updateData = ServicePricingRule::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/service-pricing-rules/{$servicePricingRule->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_servicePricingRule(): void
    {
        $servicePricingRule = ServicePricingRule::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/service-pricing-rules/{$servicePricingRule->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('service-pricing-rules', [
            'id' => $servicePricingRule->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/service-pricing-rules');

        $response->assertUnauthorized();
    }
}
