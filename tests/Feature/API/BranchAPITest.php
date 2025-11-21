<?php

declare(strict_types=1);

namespace Tests\Feature\API;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BranchAPITest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_branches(): void
    {
        Branch::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/branches');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_branch(): void
    {
        $branchData = [
            'name' => 'Main Branch',
            'phone' => '+902121234567',
            'email' => 'main@salon.com',
            'address' => '123 Main St',
            'city' => 'Istanbul',
            'country' => 'Turkey',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/branches', $branchData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Main Branch')
            ->assertJsonPath('data.city', 'Istanbul');

        $this->assertDatabaseHas('branches', [
            'name' => 'Main Branch',
            'city' => 'Istanbul',
        ]);
    }

    public function test_can_show_branch(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->getJson("/api/v1/branches/{$branch->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $branch->id)
            ->assertJsonPath('data.name', $branch->name);
    }

    public function test_can_update_branch(): void
    {
        $branch = Branch::factory()->create();

        $updateData = [
            'name' => 'Updated Branch Name',
        ];

        $response = $this->putJson("/api/v1/branches/{$branch->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Branch Name');

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Updated Branch Name',
        ]);
    }

    public function test_can_delete_branch(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->deleteJson("/api/v1/branches/{$branch->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('branches', ['id' => $branch->id]);
    }

    public function test_validation_fails_for_invalid_data(): void
    {
        $invalidData = [
            'name' => '',  // Required field
        ];

        $response = $this->postJson('/api/v1/branches', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_requires_authentication(): void
    {
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/v1/branches');

        $response->assertStatus(401);
    }
}
