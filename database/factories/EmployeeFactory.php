<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => \App\Models\Branch::factory(),
            'user_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->unique()->numerify('+90##########'),
            'email' => fake()->unique()->safeEmail(),
            'specialties' => json_encode([fake()->word(), fake()->word()]),
            'commission_rate' => fake()->randomFloat(2, 0, 99.99),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withUser(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => \App\Models\User::factory(),
        ]);
    }
}
