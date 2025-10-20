<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AppointmentHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppointmentHistory>
 */
class AppointmentHistoryFactory extends Factory
{
    protected $model = AppointmentHistory::class;

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
