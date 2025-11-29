<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Kira', 'Elektrik', 'Su', 'Maaş', 'Malzeme', 'Temizlik', 'Pazarlama', 'Diğer'];
        $paymentMethods = ['nakit', 'kredi kartı', 'banka transferi', 'çek'];

        return [
            'branch_id' => \App\Models\Branch::factory(),
            'category' => fake()->randomElement($categories),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'amount' => fake()->randomFloat(2, 50, 5000),
            'expense_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'payment_method' => fake()->randomElement($paymentMethods),
            'receipt_number' => fake()->optional(0.7)->bothify('FIS-####-????'),
        ];
    }
}
