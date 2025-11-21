<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_be_created(): void
    {
        $branch = Branch::factory()->create();

        $customer = Customer::create([
            'branch_id' => $branch->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+905551234567',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+905551234567',
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    public function test_customer_belongs_to_branch(): void
    {
        $branch = Branch::factory()->create();
        $customer = Customer::factory()->create(['branch_id' => $branch->id]);

        $this->assertInstanceOf(Branch::class, $customer->branch);
        $this->assertEquals($branch->id, $customer->branch->id);
    }

    public function test_customer_has_uuid(): void
    {
        $customer = Customer::factory()->create();

        $this->assertNotNull($customer->id);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $customer->id
        );
    }

    public function test_customer_soft_deletes(): void
    {
        $customer = Customer::factory()->create();
        $customerId = $customer->id;

        $customer->delete();

        $this->assertSoftDeleted('customers', ['id' => $customerId]);
        $this->assertNull(Customer::find($customerId));
        $this->assertNotNull(Customer::withTrashed()->find($customerId));
    }
}
