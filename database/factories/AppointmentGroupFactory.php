<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AppointmentGroup;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppointmentGroup>
 */
class AppointmentGroupFactory extends Factory
{
    protected $model = AppointmentGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'name' => fake()->words(3, true) . ' Group',
            'description' => fake()->optional()->sentence(),
            'group_type' => fake()->randomElement(['class', 'workshop', 'event']),
            'max_participants' => fake()->numberBetween(5, 20),
            'appointment_date' => fake()->dateTimeBetween('now', '+30 days'),
            'start_time' => fake()->time('H:i:s'),
            'end_time' => fake()->time('H:i:s'),
            'price_per_person' => fake()->randomFloat(2, 50, 500),
            'total_price' => fake()->randomFloat(2, 100, 2000),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'notes' => fake()->optional()->paragraph(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
