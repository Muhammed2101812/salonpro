<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\StockAuditItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockAuditItem>
 */
class StockAuditItemFactory extends Factory
{
    protected $model = StockAuditItem::class;

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
