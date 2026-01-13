<?php

declare(strict_types=1);

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Invoice::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'uuid', 'exists:customers,id'],
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'invoice_number' => ['nullable', 'string', 'max:50', 'unique:invoices,invoice_number'],
            'issue_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:pending,paid,cancelled,overdue'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer',
            'customer_id.exists' => 'Selected customer does not exist',
            'branch_id.required' => 'Please select a branch',
            'branch_id.exists' => 'Selected branch does not exist',
            'invoice_number.unique' => 'This invoice number already exists',
            'due_date.after_or_equal' => 'Due date must be on or after issue date',
            'items.min' => 'Invoice must have at least one item',
            'total.min' => 'Invoice total must be greater than or equal to 0',
        ];
    }
}
