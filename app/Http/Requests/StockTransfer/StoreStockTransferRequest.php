<?php

declare(strict_types=1);

namespace App\Http\Requests\StockTransfer;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\StockTransfer::class);
    }

    public function rules(): array
    {
        return [
            'from_branch_id' => ['required', 'uuid', 'exists:branches,id', 'different:to_branch_id'],
            'to_branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'product_variant_id' => ['required', 'uuid', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'transfer_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_branch_id.required' => 'Source branch is required',
            'from_branch_id.different' => 'Source and destination branches must be different',
            'to_branch_id.required' => 'Destination branch is required',
            'product_variant_id.required' => 'Product variant is required',
            'quantity.required' => 'Quantity is required',
            'quantity.min' => 'Quantity must be at least 1',
        ];
    }
}
