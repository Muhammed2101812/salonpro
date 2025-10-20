<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CashRegisterTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashRegisterTransaction>
 */
class CashRegisterTransactionFactory extends Factory
{
    protected $model = CashRegisterTransaction::class;

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
