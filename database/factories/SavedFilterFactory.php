<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SavedFilter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavedFilter>
 */
class SavedFilterFactory extends Factory
{
    protected $model = SavedFilter::class;

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
