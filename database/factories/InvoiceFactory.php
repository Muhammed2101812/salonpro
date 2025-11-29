<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invoiceTypes = ['sales', 'purchase', 'proforma', 'credit_note', 'debit_note'];
        $statuses = ['draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled'];
        $currencies = ['TRY', 'USD', 'EUR'];

        $subtotal = fake()->randomFloat(2, 100, 10000);
        $taxAmount = fake()->randomFloat(2, 0, $subtotal * 0.2); // Up to 20% tax
        $discountAmount = fake()->randomFloat(2, 0, $subtotal * 0.15); // Up to 15% discount
        $shippingAmount = fake()->randomFloat(2, 0, 100);
        $totalAmount = $subtotal + $taxAmount - $discountAmount + $shippingAmount;
        $paidAmount = fake()->randomFloat(2, 0, $totalAmount);
        $balanceDue = $totalAmount - $paidAmount;

        $invoiceDate = fake()->dateTimeBetween('-90 days', 'now');
        $dueDate = fake()->dateTimeBetween($invoiceDate, '+30 days');

        return [
            'branch_id' => \App\Models\Branch::factory(),
            'invoice_number' => 'INV-' . fake()->unique()->numerify('######'),
            'invoice_type' => fake()->randomElement($invoiceTypes),
            'customer_id' => fake()->optional(0.8)->randomElement([\App\Models\Customer::factory()]),
            'supplier_id' => null, // Can be set when creating purchase invoice
            'invoice_date' => $invoiceDate->format('Y-m-d'),
            'due_date' => fake()->optional(0.9)->passthrough($dueDate->format('Y-m-d')),
            'payment_date' => fake()->optional(0.4)->dateTimeBetween($invoiceDate, 'now')->format('Y-m-d'),
            'status' => fake()->randomElement($statuses),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'shipping_amount' => $shippingAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_due' => $balanceDue,
            'currency' => fake()->randomElement($currencies),
            'notes' => fake()->optional(0.3)->paragraph(),
            'terms_and_conditions' => fake()->optional(0.5)->paragraph(),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
