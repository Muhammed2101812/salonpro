<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_be_created(): void
    {
        $category = ServiceCategory::factory()->create();

        $service = Service::create([
            'service_category_id' => $category->id,
            'name' => 'Haircut',
            'description' => 'Professional haircut service',
            'duration_minutes' => 30,
            'price' => 100.00,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Haircut',
            'duration_minutes' => 30,
            'price' => 100.00,
        ]);

        $this->assertInstanceOf(Service::class, $service);
    }

    public function test_service_belongs_to_category(): void
    {
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create(['service_category_id' => $category->id]);

        $this->assertInstanceOf(ServiceCategory::class, $service->category);
        $this->assertEquals($category->id, $service->category->id);
    }

    public function test_service_has_uuid(): void
    {
        $service = Service::factory()->create();

        $this->assertNotNull($service->id);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $service->id
        );
    }

    public function test_service_soft_deletes(): void
    {
        $service = Service::factory()->create();
        $serviceId = $service->id;

        $service->delete();

        $this->assertSoftDeleted('services', ['id' => $serviceId]);
    }

    public function test_service_can_be_active_or_inactive(): void
    {
        $activeService = Service::factory()->create(['is_active' => true]);
        $inactiveService = Service::factory()->create(['is_active' => false]);

        $this->assertTrue($activeService->is_active);
        $this->assertFalse($inactiveService->is_active);
    }

    public function test_service_price_is_decimal(): void
    {
        $service = Service::factory()->create(['price' => 125.50]);

        $this->assertEquals(125.50, $service->price);
        $this->assertIsNumeric($service->price);
    }
}
