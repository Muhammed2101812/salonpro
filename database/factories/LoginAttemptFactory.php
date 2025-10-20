<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LoginAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoginAttempt>
 */
class LoginAttemptFactory extends Factory
{
    protected $model = LoginAttempt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Add factory definitions here
        ];
    }
}
