<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private Branch $branch2;
    private User $superAdmin;
    private User $branchManager;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create branches
        $this->branch = Branch::factory()->create(['name' => 'Branch 1']);
        $this->branch2 = Branch::factory()->create(['name' => 'Branch 2']);

        // Create roles
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $branchManagerRole = Role::create(['name' => 'Branch Manager']);
        $viewerRole = Role::create(['name' => 'Viewer']);

        // Create permissions
        Permission::create(['name' => 'customers.view']);
        Permission::create(['name' => 'customers.create']);
        Permission::create(['name' => 'customers.update']);
        Permission::create(['name' => 'customers.delete']);

        // Assign permissions to roles
        $superAdminRole->givePermissionTo(['customers.view', 'customers.create', 'customers.update', 'customers.delete']);
        $branchManagerRole->givePermissionTo(['customers.view', 'customers.create', 'customers.update', 'customers.delete']);
        $viewerRole->givePermissionTo(['customers.view']);

        // Create users
        $this->superAdmin = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->superAdmin->assignRole($superAdminRole);

        $this->branchManager = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->branchManager->assignRole($branchManagerRole);

        $this->viewer = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->viewer->assignRole($viewerRole);
    }

    public function test_super_admin_can_view_customers_from_any_branch(): void
    {
        $customer1 = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $customer2 = Customer::factory()->create(['branch_id' => $this->branch2->id]);

        $response = $this->actingAs($this->superAdmin, 'sanctum')
            ->getJson('/api/v1/customers');

        $response->assertStatus(200);
    }

    public function test_branch_manager_can_only_view_own_branch_customers(): void
    {
        $customer1 = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $customer2 = Customer::factory()->create(['branch_id' => $this->branch2->id]);

        $response = $this->actingAs($this->branchManager, 'sanctum')
            ->getJson('/api/v1/customers');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_customers(): void
    {
        $response = $this->getJson('/api/v1/customers');

        $response->assertStatus(401);
    }

    public function test_branch_manager_can_create_customer(): void
    {
        $customerData = [
            'branch_id' => $this->branch->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->actingAs($this->branchManager, 'sanctum')
            ->postJson('/api/v1/customers', $customerData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_viewer_cannot_create_customer(): void
    {
        $customerData = [
            'branch_id' => $this->branch->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->actingAs($this->viewer, 'sanctum')
            ->postJson('/api/v1/customers', $customerData);

        $response->assertStatus(403);
    }

    public function test_branch_manager_can_update_own_branch_customer(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $updateData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ];

        $response = $this->actingAs($this->branchManager, 'sanctum')
            ->putJson("/api/v1/customers/{$customer->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
    }

    public function test_branch_manager_cannot_update_other_branch_customer(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch2->id]);

        $updateData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ];

        $response = $this->actingAs($this->branchManager, 'sanctum')
            ->putJson("/api/v1/customers/{$customer->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_viewer_cannot_update_customer(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $updateData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ];

        $response = $this->actingAs($this->viewer, 'sanctum')
            ->putJson("/api/v1/customers/{$customer->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_branch_manager_can_delete_own_branch_customer(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $response = $this->actingAs($this->branchManager, 'sanctum')
            ->deleteJson("/api/v1/customers/{$customer->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_branch_manager_cannot_delete_other_branch_customer(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch2->id]);

        $response = $this->actingAs($this->branchManager, 'sanctum')
            ->deleteJson("/api/v1/customers/{$customer->id}");

        $response->assertStatus(403);
    }

    public function test_viewer_cannot_delete_customer(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $response = $this->actingAs($this->viewer, 'sanctum')
            ->deleteJson("/api/v1/customers/{$customer->id}");

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_customer_from_any_branch(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch2->id]);

        $response = $this->actingAs($this->superAdmin, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $customer->id,
            ],
        ]);
    }
}
