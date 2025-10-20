<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BudgetPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BudgetPlan>
 */
class BudgetPlanFactory extends Factory
{
    protected $model = BudgetPlan::class;

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
