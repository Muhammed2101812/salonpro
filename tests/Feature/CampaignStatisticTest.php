<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CampaignStatistic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignStatisticTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_campaignStatistics(): void
    {
        CampaignStatistic::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/campaign-statistics');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_campaignStatistic(): void
    {
        $data = CampaignStatistic::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/campaign-statistics', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('campaign-statistics', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_campaignStatistic(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/campaign-statistics/{$campaignStatistic->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_campaignStatistic(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();
        $updateData = CampaignStatistic::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/campaign-statistics/{$campaignStatistic->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_campaignStatistic(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/campaign-statistics/{$campaignStatistic->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('campaign-statistics', [
            'id' => $campaignStatistic->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/campaign-statistics');

        $response->assertUnauthorized();
    }
}
