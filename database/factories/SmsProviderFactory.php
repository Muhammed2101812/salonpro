<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SmsProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SmsProvider>
 */
class SmsProviderFactory extends Factory
{
    protected $model = SmsProvider::class;

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
