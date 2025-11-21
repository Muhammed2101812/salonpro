<?php

declare(strict_types=1);

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'supplier_id' => ['required', 'uuid', 'exists:suppliers,id'],
            'order_number' => ['nullable', 'string', 'max:50', 'unique:purchase_orders,order_number'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'actual_delivery_date' => ['nullable', 'date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'final_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'in:pending,approved,received,cancelled'],
            'notes' => ['nullable', 'string'],
            'created_by' => ['required', 'uuid', 'exists:users,id'],

            // Items
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'uuid', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.total_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
