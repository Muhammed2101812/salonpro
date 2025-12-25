<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AppointmentGroup;
use App\Models\AppointmentGroupParticipant;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppointmentGroupParticipant>
 */
class AppointmentGroupParticipantFactory extends Factory
{
    protected $model = AppointmentGroupParticipant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => AppointmentGroup::factory(),
            'customer_id' => Customer::factory(),
            'status' => fake()->randomElement(['confirmed', 'cancelled', 'completed']),
            'joined_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'left_at' => fake()->optional(0.2)->dateTimeBetween('now', '+30 days'),
            'notes' => fake()->optional()->sentence(),
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
