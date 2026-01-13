<?php

declare(strict_types=1);

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');
        return $this->user()?->can('update', $invoice) ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'uuid', 'exists:customers,id'],
            'branch_id' => ['sometimes', 'uuid', 'exists:branches,id'],
            'issue_date' => ['sometimes', 'date'],
            'due_date' => ['sometimes', 'date', 'after_or_equal:issue_date'],
            'subtotal' => ['sometimes', 'numeric', 'min:0'],
            'tax_amount' => ['sometimes', 'numeric', 'min:0'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'total' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:pending,paid,cancelled,overdue'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
        ];
    }
}
