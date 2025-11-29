<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_be_created(): void
    {
        $branch = Branch::factory()->create();

        $employee = Employee::create([
            'branch_id' => $branch->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '+905559876543',
            'email' => 'jane@salon.com',
            'specialties' => json_encode(['Haircut', 'Coloring']),
            'commission_rate' => 15.50,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('employees', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '+905559876543',
        ]);

        $this->assertInstanceOf(Employee::class, $employee);
    }

    public function test_employee_belongs_to_branch(): void
    {
        $branch = Branch::factory()->create();
        $employee = Employee::factory()->create(['branch_id' => $branch->id]);

        $this->assertInstanceOf(Branch::class, $employee->branch);
        $this->assertEquals($branch->id, $employee->branch->id);
    }

    public function test_employee_has_uuid(): void
    {
        $employee = Employee::factory()->create();

        $this->assertNotNull($employee->id);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $employee->id
        );
    }

    public function test_employee_soft_deletes(): void
    {
        $employee = Employee::factory()->create();
        $employeeId = $employee->id;

        $employee->delete();

        $this->assertSoftDeleted('employees', ['id' => $employeeId]);
    }

    public function test_employee_can_be_active_or_inactive(): void
    {
        $activeEmployee = Employee::factory()->create(['is_active' => true]);
        $inactiveEmployee = Employee::factory()->create(['is_active' => false]);

        $this->assertTrue($activeEmployee->is_active);
        $this->assertFalse($inactiveEmployee->is_active);
    }
}
