<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EmailProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailProvider>
 */
class EmailProviderFactory extends Factory
{
    protected $model = EmailProvider::class;

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
