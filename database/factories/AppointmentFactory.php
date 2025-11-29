<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => \App\Models\Branch::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'employee_id' => \App\Models\Employee::factory(),
            'service_id' => \App\Models\Service::factory(),
            'appointment_date' => fake()->dateTimeBetween('now', '+30 days'),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60, 90, 120]),
            'price' => fake()->randomFloat(2, 50, 500),
            'status' => 'pending',
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

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
