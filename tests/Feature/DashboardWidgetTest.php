<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DashboardWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_dashboardWidgets(): void
    {
        DashboardWidget::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard-widgets');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_dashboardWidget(): void
    {
        $data = DashboardWidget::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/dashboard-widgets', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('dashboard-widgets', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_dashboardWidget(): void
    {
        $dashboardWidget = DashboardWidget::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/dashboard-widgets/{$dashboardWidget->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_dashboardWidget(): void
    {
        $dashboardWidget = DashboardWidget::factory()->create();
        $updateData = DashboardWidget::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/dashboard-widgets/{$dashboardWidget->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_dashboardWidget(): void
    {
        $dashboardWidget = DashboardWidget::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/dashboard-widgets/{$dashboardWidget->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('dashboard-widgets', [
            'id' => $dashboardWidget->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/dashboard-widgets');

        $response->assertUnauthorized();
    }
}
