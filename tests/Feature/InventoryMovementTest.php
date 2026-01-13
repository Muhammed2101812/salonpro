<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\InventoryMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class InventoryMovementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        // Create and assign permissions
        $permissions = ['inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $this->user->givePermissionTo($permissions);
    }

    public function test_can_list_inventoryMovements(): void
    {
        InventoryMovement::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/inventory-movements');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_inventoryMovement(): void
    {
        $data = InventoryMovement::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/inventory-movements', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('inventory-movements', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_inventoryMovement(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/inventory-movements/{$inventoryMovement->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_inventoryMovement(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();
        $updateData = InventoryMovement::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/inventory-movements/{$inventoryMovement->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_inventoryMovement(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/inventory-movements/{$inventoryMovement->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('inventory-movements', [
            'id' => $inventoryMovement->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/inventory-movements');

        $response->assertUnauthorized();
    }
}
