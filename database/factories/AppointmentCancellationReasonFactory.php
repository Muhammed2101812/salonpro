<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AppointmentCancellationReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppointmentCancellationReason>
 */
class AppointmentCancellationReasonFactory extends Factory
{
    protected $model = AppointmentCancellationReason::class;

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
