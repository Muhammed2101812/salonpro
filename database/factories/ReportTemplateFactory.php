<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ReportTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportTemplate>
 */
class ReportTemplateFactory extends Factory
{
    protected $model = ReportTemplate::class;

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
