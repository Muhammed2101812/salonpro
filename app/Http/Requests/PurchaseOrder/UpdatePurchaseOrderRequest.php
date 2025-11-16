<?php

declare(strict_types=1);

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $purchaseOrderId = $this->route('purchase_order');

        return [
            'branch_id' => ['sometimes', 'required', 'uuid', 'exists:branches,id'],
            'supplier_id' => ['sometimes', 'required', 'uuid', 'exists:suppliers,id'],
            'order_number' => ['sometimes', 'string', 'max:50', "unique:purchase_orders,order_number,{$purchaseOrderId}"],
            'order_date' => ['sometimes', 'required', 'date'],
            'expected_delivery_date' => ['nullable', 'date'],
            'actual_delivery_date' => ['nullable', 'date'],
            'total_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'final_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'in:pending,approved,received,cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
