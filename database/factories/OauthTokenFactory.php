<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OauthToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OauthToken>
 */
class OauthTokenFactory extends Factory
{
    protected $model = OauthToken::class;

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
