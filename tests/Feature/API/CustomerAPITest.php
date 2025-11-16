<?php

declare(strict_types=1);

namespace Tests\Feature\API;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerAPITest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_customers(): void
    {
        Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/customers');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_customer(): void
    {
        $branch = Branch::factory()->create();

        $customerData = [
            'branch_id' => $branch->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+905551234567',
            'email' => 'john@example.com',
        ];

        $response = $this->postJson('/api/v1/customers', $customerData);

        $response->assertStatus(201)
            ->assertJsonPath('data.first_name', 'John')
            ->assertJsonPath('data.last_name', 'Doe');

        $this->assertDatabaseHas('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_can_show_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson("/api/v1/customers/{$customer->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $customer->id)
            ->assertJsonPath('data.first_name', $customer->first_name);
    }

    public function test_can_update_customer(): void
    {
        $customer = Customer::factory()->create();

        $updateData = [
            'first_name' => 'Updated Name',
        ];

        $response = $this->putJson("/api/v1/customers/{$customer->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.first_name', 'Updated Name');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'first_name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson("/api/v1/customers/{$customer->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_can_get_customer_timeline(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson("/api/v1/customers/{$customer->id}/timeline");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_get_customer_stats(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson("/api/v1/customers/{$customer->id}/stats");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_appointments',
                    'total_spent',
                    'last_appointment',
                ],
            ]);
    }

    public function test_requires_authentication(): void
    {
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/v1/customers');

        $response->assertStatus(401);
    }
}
