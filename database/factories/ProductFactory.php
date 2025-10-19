<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Saç Bakım', 'Cilt Bakım', 'Makyaj', 'Oje', 'Aksesuar', 'Kozmetik'];
        $units = ['adet', 'ml', 'gr', 'kutu', 'şişe'];

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'barcode' => fake()->boolean(70) ? fake()->ean13() : null,
            'sku' => fake()->boolean(80) ? fake()->unique()->bothify('SKU-####-????') : null,
            'price' => fake()->randomFloat(2, 10, 500),
            'cost_price' => fake()->randomFloat(2, 5, 250),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'min_stock_quantity' => fake()->numberBetween(5, 20),
            'unit' => fake()->randomElement($units),
            'category' => fake()->randomElement($categories),
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }
}
