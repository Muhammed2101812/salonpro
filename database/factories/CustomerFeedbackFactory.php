<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CustomerFeedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerFeedback>
 */
class CustomerFeedbackFactory extends Factory
{
    protected $model = CustomerFeedback::class;

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
