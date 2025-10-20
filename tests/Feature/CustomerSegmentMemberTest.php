<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CustomerSegmentMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSegmentMemberTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_customerSegmentMembers(): void
    {
        CustomerSegmentMember::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/customer-segment-members');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_customerSegmentMember(): void
    {
        $data = CustomerSegmentMember::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/customer-segment-members', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('customer-segment-members', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_customerSegmentMember(): void
    {
        $customerSegmentMember = CustomerSegmentMember::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/customer-segment-members/{$customerSegmentMember->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_customerSegmentMember(): void
    {
        $customerSegmentMember = CustomerSegmentMember::factory()->create();
        $updateData = CustomerSegmentMember::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/customer-segment-members/{$customerSegmentMember->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_customerSegmentMember(): void
    {
        $customerSegmentMember = CustomerSegmentMember::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/customer-segment-members/{$customerSegmentMember->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('customer-segment-members', [
            'id' => $customerSegmentMember->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/customer-segment-members');

        $response->assertUnauthorized();
    }
}
