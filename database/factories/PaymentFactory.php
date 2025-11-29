<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentMethods = ['cash', 'credit_card', 'debit_card', 'bank_transfer'];
        $statuses = ['pending', 'completed', 'failed', 'refunded'];

        return [
            'appointment_id' => null, // Can be set when creating payment for appointment
            'sale_id' => null, // Can be set when creating payment for sale
            'customer_id' => \App\Models\Customer::factory(),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'payment_method' => fake()->randomElement($paymentMethods),
            'payment_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement($statuses),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
