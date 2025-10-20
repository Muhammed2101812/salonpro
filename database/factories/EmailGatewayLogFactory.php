<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EmailGatewayLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailGatewayLog>
 */
class EmailGatewayLogFactory extends Factory
{
    protected $model = EmailGatewayLog::class;

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
