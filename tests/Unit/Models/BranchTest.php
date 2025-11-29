<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_can_be_created(): void
    {
        $branch = Branch::create([
            'name' => 'Main Branch',
            'code' => 'MAIN01',
            'phone' => '+902121234567',
            'email' => 'main@salon.com',
            'address' => '123 Main St',
            'city' => 'Istanbul',
            'country' => 'Turkey',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'Main Branch',
            'code' => 'MAIN01',
            'city' => 'Istanbul',
        ]);

        $this->assertInstanceOf(Branch::class, $branch);
    }

    public function test_branch_has_uuid(): void
    {
        $branch = Branch::factory()->create();

        $this->assertNotNull($branch->id);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $branch->id
        );
    }

    public function test_branch_soft_deletes(): void
    {
        $branch = Branch::factory()->create();
        $branchId = $branch->id;

        $branch->delete();

        $this->assertSoftDeleted('branches', ['id' => $branchId]);
    }

    public function test_branch_can_be_active_or_inactive(): void
    {
        $activeBranch = Branch::factory()->create(['is_active' => true]);
        $inactiveBranch = Branch::factory()->create(['is_active' => false]);

        $this->assertTrue($activeBranch->is_active);
        $this->assertFalse($inactiveBranch->is_active);
    }
}
