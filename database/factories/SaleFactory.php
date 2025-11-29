<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentMethods = ['cash', 'credit_card', 'debit_card', 'bank_transfer'];
        $subtotal = fake()->randomFloat(2, 50, 5000);
        $discount = fake()->randomFloat(2, 0, $subtotal * 0.2); // Up to 20% discount
        $tax = fake()->randomFloat(2, 0, ($subtotal - $discount) * 0.2); // Up to 20% tax
        $total = $subtotal - $discount + $tax;

        return [
            'customer_id' => fake()->optional(0.8)->randomElement([\App\Models\Customer::factory()]),
            'employee_id' => fake()->optional(0.7)->randomElement([\App\Models\Employee::factory()]),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => fake()->randomElement($paymentMethods),
            'sale_date' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
